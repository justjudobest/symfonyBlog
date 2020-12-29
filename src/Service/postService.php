<?php


namespace App\Service;


use App\Entity\Category;
use App\Entity\Post;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class postService extends AbstractController
{
    public function import(CategoryRepository $categoryRepository, PostRepository $postRepository)
    {
        if (file_exists('exportPost.json')) {
            $entityManager = $this->getDoctrine()->getManager();
            $filePosts = file_get_contents('exportPost.json');
            $posts = json_decode($filePosts);

            if (isset($posts->posts) && $posts->posts) {
                foreach ($posts->posts as $post)
                {
                    $pId = $postRepository->findOneBy(['title' => $post->title]);
                    if ($pId) {
                        $date = new \DateTime($post->date);
                        $pId->setTitle($post->title);
                        $pId->setCreated($date);
                        $pId->setSubheadline($post->subheadline);
                        $pId->setDescription($post->description);
                        $pId->setImage('asd.jpg');
                    }
                    if (!$pId) {
                        $newPost = new Post();
                        $date = new \DateTime($post->date);
                        $newPost->setTitle($post->title);
                        $newPost->setCreated($date);
                        $newPost->setSubheadline($post->subheadline);
                        $newPost->setDescription($post->description);
                        $newPost->setImage('asd.jpg');
                        foreach ($post->categories as $categories) {
                            $category = $categoryRepository->findOneBy(['name' => $categories]);
                            if(!$category) {
                                $category = new Category();
                            }
                            $newPost->addCategories($category->setName($categories));
                            $entityManager->persist($category);
                            $entityManager->flush();
                        }
//                        $this->deleteAllCategories($categoryRepository);
                        $entityManager->persist($newPost);
                    }
                }
                $entityManager->flush();
            }

        }
    }


}