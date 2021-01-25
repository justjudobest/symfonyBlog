<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class CategoryFixtures extends Fixture
{
    /**
     * @var Generator
     */
    protected $faker;
    public function load(ObjectManager $manager)
    {
        $this->faker = Factory::create();
        $this->addCategories($manager);
        $manager->flush();
    }

    private function addCategories(EntityManager $em)
    {
//        $post = $em->getRepository(Post::class)->find('post_id');
        for($i=0;$i<10;$i++) {

            $name = $this->faker->jobTitle;
            $category = new Category();
            $category->setName($name);
//            $category->setPosts($post);

            $em->persist($category);
        }

    }
}
