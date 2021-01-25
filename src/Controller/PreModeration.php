<?php


namespace App\Controller;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;





/**
 * @Route("/admin/PreModeration")
 */
class PreModeration extends AbstractController
{

    /**
     * @Route("/", name="PreModeration", methods={"GET"})
     */
    public function PreModeration(CommentRepository $commentRepository)
    {
        $countComment = $commentRepository->findBy(['PreModeration' => 0]);

        return $this->render('comment/pre-moderation.html.twig', [
            'comments' => $commentRepository->findAll(),
            'countComment' => $countComment,

        ]);

    }


}