<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\PrePersist;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Booking
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $booker;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ad", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ad;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="Veuillez entrer une date valide ")
     * @Assert\GreaterThan("today" ,  message="La date d'arrivée doit être ultérieure à la date d'aujourd'hui !")
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="Veuillez entrer une date valide ")
     * @Assert\GreaterThan(propertyPath="startDate", message="La date de départ doit être superieur à la date d'arrivée !")
     */
    private $endDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    public function getId() : ? int
    {
        return $this->id;
    }

    public function getBooker() : ? User
    {
        return $this->booker;
    }

    public function setBooker(? User $booker) : self
    {
        $this->booker = $booker;

        return $this;
    }

    public function getAd() : ? Ad
    {
        return $this->ad;
    }

    public function setAd(? Ad $ad) : self
    {
        $this->ad = $ad;

        return $this;
    }

    public function getStartDate() : ? \DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate) : self
    {
        $this->startDate = $startDate;

        return $this;
    }


    public function getEndDate() : ? \DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate) : self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCreatedAt() : ? \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt) : self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAmount() : ? float
    {
        return $this->amount;
    }

    public function setAmount(float $amount) : self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComment() : ? string
    {
        return $this->comment;
    }

    public function setComment(? string $comment) : self
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Permet d'enregister des champs dans la base automatiquement
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        if (empty($this->createdAt)) {
            $this->createdAt = new \DateTime();
        }

        if (empty($this->amount)) {
            $this->amount = $this->ad->getPrice() * $this->getDuration();
        }

    }

    public function getDuration()
    {
        $diff = $this->endDate->diff($this->startDate);
        return $diff->days;
    }

    public function isBookableDates()
    {
        $noteAvailableDays = $this->ad->getNotAvailableDays();

        $bookingDays = $this->getDays();

        $formatDay = function ($day) {
            return $day->format('Y-m-d');
        };

        // Tableau des chaines de caractères de mes journées
        $days = array_map($formatDay, $bookingDays);

        // Tableau des chaines de caractères de mes journées
        $notAvailable = array_map($formatDay, $noteAvailableDays);

        dump($notAvailable);
        //die;

        foreach ($days as $day) {
            if (array_search($day, $notAvailable) !== false) {
                return false;
            }
        }
        return true;
    }

    /**
     * Permet de récupérer un tableau des days qui correspondent à ma reservation
     * 
     */
    public function getDays()
    {
        $notAvailableDays = [];
        $resultat = range(
            $this->startDate->getTimestamp(),
            $this->endDate->getTimestamp(),
            24 * 60 * 60
        );

        $days = array_map(function ($daysTimestamp) {
            return new \DateTime(date('Y-m-d', $daysTimestamp));
        }, $resultat);

        $notAvailableDays = array_merge($notAvailableDays, $days);

        return $notAvailableDays;
    }
}
