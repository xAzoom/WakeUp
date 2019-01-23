<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Doctrine\PhotoRepository")
 */
class Photo
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", unique=true)
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Image(
     *     minWidth = 200,
     *     minHeight = 200,
     * )
     */
    protected $image;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Account", inversedBy="photos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $format;

    public function setId(string $id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setImage($file = null)
    {
        $this->image = $file;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function getOwner(): ?Account
    {
        return $this->owner;
    }

    public function setOwner(?Account $account): self
    {
        $this->owner = $account;

        return $this;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function getLink(): string
    {
        return $this->getId().".".$this->getFormat();
    }
}
