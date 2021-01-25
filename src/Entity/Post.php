<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
class Post
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     */
    private $created;

    /**
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subheadline;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Upload your image")
     * @Assert\Image(mimeTypes={ "image/png", "image/jpeg" })
     */
    private $image;


    /**
     * @var
     * @Assert\NotBlank
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", inversedBy="posts", cascade={"persist"} )
     */
    protected $categories;

    /**
     * @ORM\ManyToOne(targetEntity=AdminUser::class, inversedBy="posts")
     * A
     */
    private $AdminUsers;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="post")
     */
    private $Comments;

    /**
     * @ORM\Column(type="boolean")
     */
    private $activ;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="posts")
     */
    private $Users;

    /**
     * @ORM\Column(type="boolean")
     */
    private $notRegistered;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->Comments = new ArrayCollection();
        $this->Users = new ArrayCollection();
    }


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

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getSubheadline(): ?string
    {
        return $this->subheadline;
    }

    public function setSubheadline(?string $subheadline): self
    {
        $this->subheadline = $subheadline;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image): void
    {
        $this->image = $image;
    }

    /**
     * @return ArrayCollection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param mixed $categories
     */
    public function setCategories($categories): void
    {
        $this->categories = $categories;
    }

    public function addCategories(Category $category)
    {
        if (!$this->categories->contains($category)) {

            $this->categories->add($category);
        }

    }

    public function removeCategories(Category $category)
    {
        $this->categories->removeElement($category);
    }

    public function __toString()
    {
        return $this->title;
    }

    public function getAdminUsers(): ?AdminUser
    {
        return $this->AdminUsers;
    }

    public function setAdminUsers(?AdminUser $AdminUsers): self
    {
        $this->AdminUsers = $AdminUsers;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->Comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->Comments->contains($comment)) {
            $this->Comments[] = $comment;
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->Comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
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

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->Users;
    }

    public function addUser(User $user): self
    {
        if (!$this->Users->contains($user)) {
            $this->Users[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->Users->removeElement($user);

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
