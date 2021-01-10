<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/comment")
 */
class CommentController extends AbstractController
{
    /**
     * @Route("/", name="comment_index", methods={"GET"})
     */
    public function index(CommentRepository $commentRepository): Response
    {

        return $this->render('comment/index.html.twig', [
            'comments' => $commentRepository->findAll(),


        ]);
    }

    /**
     * @Route("/new/", name="comment_new", methods={"GET","POST"})
     */
    public function new(Request $request, PostRepository $postRepository, CommentRepository $commentRepository): Response
    {
        $comment = new Comment();
        $text = $request->get('message');
        $id = $request->get('id');
        $name = $request->get('contact-name');

        if ($text == '' || $name == '' || strlen($text) > 255) {
            return $this->redirect("http://localhost/home/$id");
        }

        $date = new \DateTime();
        $comment->setCreated($date);
        $comment->setText($text);
        $comment->setSenderName($name);
        $comment->setActiv(True);

        $arrayPost = $postRepository->findAll();
        foreach ($arrayPost as $objectsPost) {
            if($objectsPost->getId() == $id) {
                $comment->setPost($objectsPost);
            }
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($comment);
        $entityManager->flush();

        return $this->redirect("http://localhost/home/$id");
    }

    /**
     * @Route("/{id}", name="comment_show", methods={"GET"})
     */
    public function show(Comment $comment): Response
    {
        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="comment_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Comment $comment): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('comment_index');
        }

        return $this->render('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="comment_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Comment $comment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('comment_index');
    }
}
