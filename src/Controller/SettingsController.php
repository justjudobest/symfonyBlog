<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use phpDocumentor\Reflection\Types\True_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends AbstractController
{
    /**
     * @Route("/admin/settings", name="settings")
     */
    public function index(): Response
    {

        return $this->render('settings/index.html.twig', [
            'controller_name' => 'SettingsController',
        ]);
    }

    /**
     * @param CommentRepository $commentRepository
     * @Route("/admin/commentOff", name="commentOff", methods={"GET","POST"})
     */
    public function CommentOff(CommentRepository $commentRepository)
    {

        $arrayComments = $commentRepository->findAll();
        foreach ($arrayComments as $objectComments) {
            if ($objectComments->getActiv() == True) {
                $activ = $objectComments->setActiv(0);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($activ);
            }
            else {
                $activ = $objectComments->setActiv(1);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($activ);
            }
        }
        $entityManager->flush();
        return $this->redirectToRoute('settings');

    }
}
