<?php

namespace App\Entity;

use App\Entity\User;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;



/**
 * @ORM\Entity(repositoryClass="App\Repository\AdRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(
 *     fields={"title"},
 *     message="Une autre annonce possede déjà ce titre merci de le modifier"
 * )
 */
class Ad
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     *  @Assert\Length(
     *      min = 10,
     *      max = 255,
     *      minMessage = "Le titre doit avoir plus de  {{ limit }} caractères",
     *      maxMessage = "Le titre doit avoir moin de  {{ limit }} caractères"
     * )
     * 
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="text")
     * 
     * @Assert\Length(
     *      min = 10,
     *      max = 255,
     *      minMessage = "L'introduction doit avoir plus de  {{ limit }} caractères",
     *      maxMessage = "L'introduction  doit avoir moin de  {{ limit }} caractères"
     * )
     */
    private $introduction;

    /**
     * @ORM\Column(type="text")
     * 
     * @Assert\Length(
     *      min = 100,
     *      minMessage = "La description doit avoir plus de  {{ limit }} caractères",
     * )
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     *  @Assert\Url(
     *      message = "L'url '{{ value }}' n'est pas un url valide",
     * )
     */
    private $coverImage;

    /**
     * @ORM\Column(type="integer")
     */
    private $rooms;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Image", mappedBy="ad", orphanRemoval=true)
     * @Assert\Valid()
     */
    private $images;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="ads")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Booking", mappedBy="ad")
     */
    private $bookings;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="ad", orphanRemoval=true)
     */
    private $comments;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId() : ? int
    {
        return $this->id;
    }

    public function getTitle() : ? string
    {
        return $this->title;
    }

    public function setTitle(string $title) : self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug() : ? string
    {
        return $this->slug;
    }

    public function setSlug(string $slug) : self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPrice() : ? float
    {
        return $this->price;
    }

    public function setPrice(float $price) : self
    {
        $this->price = $price;

        return $this;
    }

    public function getIntroduction() : ? string
    {
        return $this->introduction;
    }

    public function setIntroduction(string $introduction) : self
    {
        $this->introduction = $introduction;

        return $this;
    }

    public function getContent() : ? string
    {
        return $this->content;
    }

    public function setContent(string $content) : self
    {
        $this->content = $content;

        return $this;
    }

    public function getCoverImage() : ? string
    {
        return $this->coverImage;
    }

    public function setCoverImage(string $coverImage) : self
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    public function getRooms() : ? int
    {
        return $this->rooms;
    }

    public function setRooms(int $rooms) : self
    {
        $this->rooms = $rooms;

        return $this;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * 
     */
    public function initialiseSlug()
    {
        $slug = new Slugify();
        if (empty($this->slug) || $this->slug != $slug->slugify($this->title)) {
            $this->slug = $slug->slugify($this->title);
        }
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages() : Collection
    {
        return $this->images;
    }

    public function addImage(Image $image) : self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setAd($this);
        }

        return $this;
    }

    public function removeImage(Image $image) : self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getAd() === $this) {
                $image->setAd(null);
            }
        }

        return $this;
    }

    public function getAuthor() : ? User
    {
        return $this->author;
    }

    public function setAuthor(? User $author) : self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection|Booking[]
     */
    public function getBookings() : Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking) : self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setAd($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking) : self
    {
        if ($this->bookings->contains($booking)) {
            $this->bookings->removeElement($booking);
            // set the owning side to null (unless already changed)
            if ($booking->getAd() === $this) {
                $booking->setAd(null);
            }
        }

        return $this;
    }


    /**
     * La fonction permet de récupérer les dates non disponbile dans une annonce
     */
    public function getNotAvailableDays()
    {
        //calculer les jours qui se trouvent entre la date d'arrivée et de départ
        $notAvailableDays = [];


        foreach ($this->getBookings() as $booking) {
            $resultat = range(
                $booking->getStartDate()->getTimestamp(),
                $booking->getEndDate()->getTimestamp(),
                24 * 60 * 60
            );

            $days = array_map(function ($daysTimestamp) {
                return new \DateTime(date('Y-m-d', $daysTimestamp));
            }, $resultat);

            $notAvailableDays = array_merge($notAvailableDays, $days);
        }

        return $notAvailableDays;

    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments() : Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment) : self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAd($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment) : self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getAd() === $this) {
                $comment->setAd(null);
            }
        }

        return $this;
    }

    /**
     * Permet de récuprer la moyenne des avis 
     */
    public function getAvgRatings()
    {
            //Calculer la somme des notations

        $sum = array_reduce($this->comments->toArray(), function ($total, $comment) {
            return $total + $comment->getRating();
        }, 0);

        $countComments = count($this->comments);

        if ($countComments > 0) {
            return $sum / $countComments;
        } else {
            return 0;
        }
    }

    /**
     * Permet de récuprer les commentaires d'un user X
     */
    public function getCommentFromAuthor(User $author)
    {
        foreach ($this->comments as $comment) {
            if ($comment->getAuthor() === $author) return $comment;
        }
        return null;
    }

}
