<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 */
class Image
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
     * @Assert\url(
     *      message = "L'url n'est pas valide",
     * )
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 10,
     *      minMessage = "La caption de l'image doit avoir plus de  {{ limit }} caractères",
     * )
     */
    private $caption;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ad", inversedBy="images")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ad;

    public function getId() : ? int
    {
        return $this->id;
    }

    public function getUrl() : ? string
    {
        return $this->url;
    }

    public function setUrl(string $url) : self
    {
        $this->url = $url;

        return $this;
    }

    public function getCaption() : ? string
    {
        return $this->caption;
    }

    public function setCaption(string $caption) : self
    {
        $this->caption = $caption;

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
}
