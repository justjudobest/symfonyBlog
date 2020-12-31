<?php

namespace App\Controller;

use App\Repository\AdminUserRepository;
use App\Service\postExport;
use App\Service\postImport;
use App\Entity\Post;
use App\Form\PostType;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;




/**
 * @Route("/admin/post")
 */
class PostController extends AbstractController
{
    /**
     * @Route("/", name="post_index", methods={"GET"})
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
        $searchSubHeadline = $request->get('search-admin','');

        $post = $postRepository->searchPost($searchPost, $sort, $sortKey, $offset);


        if ($page > 1 && $page > $paged = ceil($post->count() / PostRepository::PAGINATOR_PER_PAGE)) {
            return $this->redirectToRoute('post_index', ['paged' => $paged]);
        }



        return $this->render('post/index.html.twig', [
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
     * @Route("/new", name="post_new", methods={"GET","POST"})
     */
    public function new(Request $request, AdminUserRepository  $adminUserRepository): Response
    {
        $post = new Post();
        $adminUser = $request->getSession()->get('_security.last_username');
        $ObjectAdminUser = $adminUserRepository->findOneBy(['email' => $adminUser]);
        $post->setAdminUsers($ObjectAdminUser);
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('post_index');
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/mass", name="post_mass", methods={"GET","POST"})
     */
    public function massAction(Request $request, PostRepository $postRepository): Response
    {
        $postId = $postRepository->findBy(['id' => $request->get('postChekbox')]);

        foreach ($postId as $post) {


            if ($post) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($post);
                $entityManager->flush();
            }
        }
        return $this->redirectToRoute('post_index');
    }

    /**
     * @Route("/{id}", name="post_show", methods={"GET"})
     */
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="post_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Post $post): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('post_index');
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="post_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Post $post): Response
    {

        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('post_index');
    }

    /**
     * @Route("/export", name="post_export", methods={"POST"})
     */
    public function exportFile(PostRepository $postRepository, postExport $postExport)
    {
        $postExport->export($postRepository);
        $postExport->file_force_download('exportPost.json');
        return $this->redirectToRoute('post_index');
    }

    /**
     * @Route("/import", name="post_import", methods={"POST"})
     */
    public function importFile(CategoryRepository $categoryRepository, PostRepository $postRepository, postImport $postService)
    {
        $postService->import($categoryRepository, $postRepository);
        return $this->redirectToRoute('post_index');
    }

    /**
     * @Route("/deleteall", name="categories_delete", methods={"POST"})
     */
    public function deleteAllCategories(CategoryRepository $categoryRepository)
    {
        $categoryId = $categoryRepository->findAll();
        foreach ($categoryId as $category) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($category);
            $entityManager->flush();
        }
        return $this->redirectToRoute('category_index');
    }
}


