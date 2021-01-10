<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index(Request $request, PostRepository $postRepository): Response
    {
        $offset = max(0, $request->query->getInt('paged'));
        $page = 1;

        if ($offset >= 1) {
            $page = $offset;
            $offset = $offset * PostRepository::PAGINATOR_PER_PAGE - PostRepository::PAGINATOR_PER_PAGE;
        }

        $sortKey = $request->get('order_key');
        $sort = $request->get('order');
        $searchPost = $request->get('search-post', '');
//        $searchSubHeadline = $request->get('search-subheadline','');
//        $searchSubHeadline = $postRepository->searchsubheadline($searchSubHeadline);
        $post = $postRepository->searchPost($searchPost, $sort, $sortKey, $offset);

        if ($page > 1 && $page > $paged = ceil($post->count() / PostRepository::PAGINATOR_PER_PAGE)) {
            return $this->redirectToRoute('post_index', ['paged' => $paged]);
        }

        return $this->render('home/index.html.twig', [
            'posts' => $post,
            'sort' => $sort,
            'previous' => $page - 1,
            'next' => $page + 1,
            'offset' => $offset,
            'limit' => PostRepository::PAGINATOR_PER_PAGE,
            'paged' => $page,
        ]);
    }

    /**
     * @Route("/home/{id}", name="singlePost", methods={"GET"})
     */
    public function singlePost(Post $post, Comment $comment)
    {

        return $this->render('home/singlePost.html.twig',[
            'post' => $post,
            'comments' => $comment

        ]);
    }
}


