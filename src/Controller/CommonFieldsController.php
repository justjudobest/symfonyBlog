<?php

namespace App\Controller;

use App\Entity\CommonFields;
use App\Form\CommonFieldsType;
use App\Repository\CommonFieldsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/common/fields")
 */
class CommonFieldsController extends AbstractController
{
    /**
     * @Route("/", name="common_fields_index", methods={"GET"})
     */
    public function index(CommonFieldsRepository $commonFieldsRepository): Response
    {
        return $this->render('common_fields/index.html.twig', [
            'common_fields' => $commonFieldsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="common_fields_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $commonField = new CommonFields();
        $form = $this->createForm(CommonFieldsType::class, $commonField);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $commonField->getImage();
            $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();

            if (!file_exists("upload/gallery/$fileName")) {
                $file->move(
                    $this->getParameter('gallery_upload'),
                    $fileName
                );
                $commonField->setImage($fileName);

            } else {

                $newFileName = $fileName;
                while ($newFileName == $fileName) {
                    $newFileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();
                }
                $file->move(
                    $this->getParameter('gallery_upload'),
                    $newFileName
                );
                $commonField->setImage($newFileName);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($commonField);
            $entityManager->flush();

            return $this->redirectToRoute('common_fields_index');
        }

        return $this->render('common_fields/new.html.twig', [
            'common_field' => $commonField,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="common_fields_show", methods={"GET"})
     */
    public function show(CommonFields $commonField): Response
    {
        return $this->render('common_fields/show.html.twig', [
            'common_field' => $commonField,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="common_fields_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, CommonFields $commonField): Response
    {
        $commonField->setimage('');
        $form = $this->createForm(CommonFieldsType::class, $commonField);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $commonField->getImage();
            $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();

            if (!file_exists("upload/gallery/$fileName")) {

                $file->move(
                    $this->getParameter('gallery_upload'),
                    $fileName
                );
                $commonField->setImage($fileName);

            } else {

                $newFileName = $fileName;
                while ($newFileName == $fileName) {
                    $newFileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();
                }
                $file->move(
                    $this->getParameter('gallery_upload'),
                    $newFileName
                );
                $commonField->setImage($newFileName);
            }
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('settings');
        }


        return $this->render('common_fields/edit.html.twig', [
            'common_field' => $commonField,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="common_fields_delete", methods={"DELETE"})
     */
    public function delete(Request $request, CommonFields $commonField): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commonField->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($commonField);
            $entityManager->flush();
        }

        return $this->redirectToRoute('common_fields_index');
    }

    private function generateUniqueFileName()
    {
        // md5() уменьшает схожесть имён файлов, сгенерированных
        // uniqid(), которые основанный на временных отметках
        return md5(uniqid());
    }
}
