<?php

namespace App\Controller;


use App\Entity\CommonFields;
use App\Form\CommonFieldsType;
use App\Form\PostType;
use App\Repository\CommentRepository;
use App\Repository\CommonFieldsRepository;
use App\Repository\ModerationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;






class SettingsController extends AbstractController
{
    /**
     * @Route("/admin/settings", name="settings", methods={"GET","POST"})
     */
    public function index(
        ModerationRepository $moderationRepository,
        CommentRepository $commentRepository,
        CommonFieldsRepository $commonFieldsRepository,
        Request $request


    ): Response
    {
        $commonField = new CommonFields();
        $form = $this->createForm(CommonFieldsType::class, $commonField);
        $form->handleRequest($request);



        $commonFields = $commonFieldsRepository->find(['id' => 1]);
        $arrayValue = $moderationRepository->findBy(['id' => 1]);
        $arrayComments = $commentRepository->findBy(['activ' => 1]);
        $activ = 0;
        $arrayNotRegistered = $commentRepository->findBy(['notRegistered' => 1]);
        $notRegistered = 0;

        foreach ($arrayNotRegistered as $objectNotRegistered) {
            if ($objectNotRegistered->getNotRegistered() == 1) {
                $notRegistered = $objectNotRegistered->getNotRegistered();
            }
        }

        foreach ($arrayComments as $objectComments) {
            if ($objectComments->getActiv() == 1) {
                $activ = $objectComments->getActiv();
            }
        }

        return $this->render('settings/index.html.twig', [
            'controller_name' => 'SettingsController',
            'moderation' => $arrayValue,
            'activ' => $activ,
            'notRegistered' => $notRegistered,
            'commonFields' => $commonFields,
            'form' => $form->createView(),

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
            if ($objectComments->getActiv() == 1 ) {
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
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        return $this->redirectToRoute('settings');

    }

    /**
     * @Route("/admin/CommentsModeration", name="CommentsModeration", methods={"GET","POST"})
     */
    public function CommentsModeration(ModerationRepository $moderationRepository)
    {
        $arrayValue = $moderationRepository->findBy(['id' => 1]);
        foreach ($arrayValue as $value) {
            if ($value->getValue() == 0) {
                $value->setValue('1');
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($value);
            } else {
                $value->setValue('0');
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($value);
            }

        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return $this->redirectToRoute('settings');
    }

    /**
     * @Route("/admin/notRegisteredUser", name="notRegisteredUser", methods={"GET","POST"})
     */
    public function notRegisteredUser(CommentRepository $commentRepository)
    {
        $arrayComments = $commentRepository->findAll();

        foreach ($arrayComments as $objectComments) {
            if ($objectComments->getNotRegistered() == 1) {
                $notRegistered = $objectComments->setNotRegistered(0);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($notRegistered);
            } else {
                $notRegistered = $objectComments->setNotRegistered(1);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($notRegistered);

            }
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();


        return $this->redirectToRoute('settings');
    }

    /**
     * @Route("/admin/ChangeNameSite/{id}", name="ChangeNameSite", methods={"POST"})
     * @param CommonFields $commonFields
     * @param Request $request
     */
    public function ChangeNameSite(CommonFields $commonFields, Request $request)
    {

        $nameSite = $request->get('name_site');
        $commonFields->setName($nameSite);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($commonFields);
        $entityManager->flush();
        return $this->redirectToRoute('settings');
    }

    /**
     * @Route("/admin/ChangeContactsSite/{id}", name="ChangeContactsSite", methods={"POST"})
     * @param CommonFields $commonFields
     * @param Request $request
     */
    public function ChangeContactsSite(CommonFields $commonFields, Request $request)
    {

        $contactsSite = $request->get('contacts_site');
        $commonFields->setContacts($contactsSite);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($commonFields);
        $entityManager->flush();
        return $this->redirectToRoute('settings');
    }

}
