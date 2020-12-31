<?php


namespace App\Service;


use App\Entity\Category;
use App\Entity\Post;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class postImport extends AbstractController
{
    private $categories = [];

    public function import(CategoryRepository $categoryRepository, PostRepository $postRepository)
    {
        if (file_exists('exportPost.json')) {
            $entityManager = $this->getDoctrine()->getManager();
            $filePosts = file_get_contents('exportPost.json');
            $posts = json_decode($filePosts);

            if (isset($posts->posts) && $posts->posts) {
                $masObject = [];
                foreach ($posts->posts as $post) {
                    $postObject = $postRepository->findOneBy(['title' => $post->title]);

                    if (!$postObject) {
                        $postObject = new Post();
                    }
                    $date = new \DateTime($post->date);
                    $postObject->setTitle($post->title);
                    $postObject->setCreated($date);
                    $postObject->setSubheadline($post->subheadline);
                    $postObject->setDescription($post->description);
                    $postObject->setImage('asd.jpg');

                    foreach ($post->categories as $categoryName) {

                        $category = null;
                        if (isset($this->categories[$categoryName]) && $this->categories[$categoryName] instanceof Category) {
                            $category = $this->categories[$categoryName];
                        }

                        if (!$category) {
                            $category = $categoryRepository->findOneBy(['name' => $categoryName]);

                            if (!$category) {
                                $category = new Category();
                            }
                        }

                        $category->setName($categoryName);
                        $this->categories[$categoryName] = $category;
                        $entityManager->persist($category);
                        $postObject->addCategories($category);
                    }

                    $masObject[] = $postObject;

                    $entityManager->persist($postObject);
                }

                $entityManager->flush();

                $postRepository->deletePosts($masObject);
            }

        }
    }


}