<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(
 *     fields={"email"},
 *     message="Un autre utilisateur possede déjà cet email merci de la modifier"
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *      message = "Le prénom ne doit pas être vide"
     * )
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     *  @Assert\NotBlank(
     *      message = "Le nom ne doit pas être vide"
     * )
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(message = "Veuillez renseigner un email valide")
     * 
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url(
     *      message = "Renseigner un url d'avatar valide."
     * )
     */
    private $picture;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $hash;

    /**
     * @Assert\EqualTo(
     *      propertyPath = "hash",
     *      message = "Les deux mots de passes ne sont pas identiques."
     * )
     */
    public $passwordConfirm;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 10,
     *      minMessage = "Votre introduction doit avoir plus de  {{ limit }} caractères"
     * )
     */
    private $introduction;

    /**
     * @ORM\Column(type="text")
     * 
     * @Assert\Length(
     *      min = 10,
     *      minMessage = "Votre déscription doit avoir plus de  {{ limit }} caractères"
     * )
     * 
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Ad", mappedBy="author", orphanRemoval=true)
     */
    private $ads;

    public function __construct()
    {
        $this->ads = new ArrayCollection();
    }

    public function getId() : ? int
    {
        return $this->id;
    }

    public function getFirstName() : ? string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName) : self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName() : ? string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName) : self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail() : ? string
    {
        return $this->email;
    }

    public function setEmail(string $email) : self
    {
        $this->email = $email;

        return $this;
    }

    public function getPicture() : ? string
    {
        return $this->picture;
    }

    public function setPicture(? string $picture) : self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getHash() : ? string
    {
        return $this->hash;
    }

    public function setHash(string $hash) : self
    {
        $this->hash = $hash;

        return $this;
    }

    public function getPasswordConfirm() : ? string
    {
        return $this->passwordConfirm;
    }

    public function setPasswordConfirm(string $passwordConfirm) : self
    {
        $this->passwordConfirm = $passwordConfirm;

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

    public function getDescription() : ? string
    {
        return $this->description;
    }

    public function setDescription(string $description) : self
    {
        $this->description = $description;

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

    /**
     * @return Collection|Ad[]
     */
    public function getAds() : Collection
    {
        return $this->ads;
    }

    public function addAd(Ad $ad) : self
    {
        if (!$this->ads->contains($ad)) {
            $this->ads[] = $ad;
            $ad->setAuthor($this);
        }

        return $this;
    }

    public function removeAd(Ad $ad) : self
    {
        if ($this->ads->contains($ad)) {
            $this->ads->removeElement($ad);
            // set the owning side to null (unless already changed)
            if ($ad->getAuthor() === $this) {
                $ad->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * 
     */
    public function initialiseSlug()
    {
        if (empty($this->slug)) {
            $slug = new Slugify();
            $this->slug = $slug->slugify($this->firstName . ' ' . $this->lastName);
        }
    }


    /*
     *Les fonctions implementées par l'interface userInterface
     */

    public function getRoles()
    {
        return array('ROLE_USER');

    }

    public function getPassword()
    {
        return $this->hash;
    }

    public function getSalt()
    {
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
    }

    public function getFullName()
    {
        return "{$this->firstName} {$this->lastName}";
    }

}
