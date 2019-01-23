<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Doctrine\PostRepository")
 * @UniqueEntity("title", message="Your title must be unique.")
 */
class Post
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message = "Please enter a title.")
     * @Assert\Length(
     *     min = 3,
     *     max = 24,
     *     minMessage = "Your title must be at least {{ limit }} characters long.",
     *     maxMessage = "Your title cannot be longer than {{ limit }} characters."
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message = "Please enter a content.")
     * @Assert\Length(
     *     min = 3,
     *     max = 250,
     *     minMessage = "Your content must be at least {{ limit }} characters long.",
     *     maxMessage = "Your content cannot be longer than {{ limit }} characters."
     * )
     */
    private $content;

    /**
     * @Assert\NotBlank(message = "You must choose a category.")
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @Assert\NotBlank(message = "You must upload a photo.")
     * @ORM\ManyToOne(targetEntity="App\Entity\Photo")
     * @ORM\JoinColumn(nullable=false)
     */
    private $photo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $photoLink;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Account", inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getPhoto(): ?Photo
    {
        return $this->photo;
    }

    public function setPhoto(?Photo $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getPhotoLink(): ?string
    {
        return $this->photoLink;
    }

    public function setPhotoLink(string $photoLink): self
    {
        $this->photoLink = $photoLink;

        return $this;
    }

    public function getCategoryName(): array
    {
        return [
            'id' => $this->category->getId(),
            'name' => $this->category->getName(),
        ];
    }

    public function getAuthor(): ?Account
    {
        return $this->author;
    }

    public function setAuthor(?Account $author): self
    {
        $this->author = $author;

        return $this;
    }
}
