<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
class Comment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @ORM\ManyToOne(targetEntity=Post::class, inversedBy="Comments")
     */
    private $post;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank()
     */
    private $created;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $SenderName;

    /**
     * @ORM\ManyToOne(targetEntity=Comment::class, inversedBy="comments")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="parent")
     */
    private $comments;

    /**
     * @ORM\Column(type="boolean")
     */
    private $activ;

    /**
     * @ORM\Column(type="boolean")
     */
    private $PreModeration;

    /**
     * @ORM\Column(type="boolean")
     */
    private $notRegistered;


    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getSenderName(): ?string
    {
        return $this->SenderName;
    }

    public function setSenderName(string $SenderName): self
    {
        $this->SenderName = $SenderName;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(self $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setParent($this);
        }

        return $this;
    }

    public function removeComment(self $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getParent() === $this) {
                $comment->setParent(null);
            }
        }

        return $this;
    }

    public function getActiv(): ?bool
    {
        return $this->activ;
    }

    public function setActiv(bool $activ): self
    {
        $this->activ = $activ;

        return $this;
    }

    public function __toString(): string
    {
        return $this->text;
    }

    public function getPreModeration(): ?bool
    {
        return $this->PreModeration;
    }

    public function setPreModeration(bool $PreModeration): self
    {
        $this->PreModeration = $PreModeration;

        return $this;
    }

    public function getNotRegistered(): ?bool
    {
        return $this->notRegistered;
    }

    public function setNotRegistered(bool $notRegistered): self
    {
        $this->notRegistered = $notRegistered;

        return $this;
    }



}
