<?php

namespace App\Entity;

use App\Repository\PostRepository;
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
     * @ORM\Column(type="date")
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
     */
    private $image;

    /**
     * @var
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", inversedBy="posts")
     */
    protected $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
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

        $category->addPosts($this);
        $this->categories->add($category);

    }

    public function removeCategories(Category $category)
    {
        $this->categories->removeElement($category);
    }




    public function __toString()
    {
        return $this->title;
    }

}
