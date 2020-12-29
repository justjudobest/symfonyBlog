<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Post;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;


class PostFixtures extends Fixture
{
    /**
     * @var Generator
     */
    protected $faker;


    public function load(ObjectManager $manager)
    {
        $this->faker = Factory::create();
        $this->addPosts($manager);
        $manager->flush();
    }



    public function addPosts(EntityManager $em) {

        $categories = $em->getRepository(Category::class)->find('id');
        for($i=0;$i<10;$i++) {

            $post = new Post();

            $image = $this->faker->imageUrl();
            $title = $this->faker->title;
            $created = $this->faker->dateTime;
            $subTitle = $this->faker->slug;
            $Description = $this->faker->text;
//            $category = $categories[array_rand($categories)];


            $post->setTitle($title);
            $post->setCreated($created);
            $post->setSubheadline($subTitle);
            $post->setDescription($Description);
//            $post->setCategories($category);
            $post->setImage($image);


            $em->persist($post);
        }



    }
}
