<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 * @ORM\Entity
 * @ORM\Table(name="category")
 */
class Category extends CreatedAndUpdated
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Post", mappedBy="categories")
     * @ORM\JoinTable(name="categories_posts",
     *     joinColumns={
     *       @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *       @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     *     }
     * )
     */
    protected $posts;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }


    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * @param mixed $posts
     */
    public function setPosts($posts): void
    {
        $this->posts = $posts;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function addPosts(Post $post)
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
        }
    }

    public function removePosts(Post $post)
    {
        $this->posts->removeElement($post);
    }

}
