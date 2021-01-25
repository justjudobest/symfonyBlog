<?php

namespace App\Controller;

use App\Entity\Restrictions;
use App\Form\RestrictionsType;
use App\Repository\RestrictionsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/restrictions")
 */
class RestrictionsController extends AbstractController
{
    /**
     * @Route("/", name="restrictions_index", methods={"GET"})
     */
    public function index(RestrictionsRepository $restrictionsRepository): Response
    {
        return $this->render('restrictions/index.html.twig', [
            'restrictions' => $restrictionsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="restrictions_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $restriction = new Restrictions();
        $form = $this->createForm(RestrictionsType::class, $restriction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($restriction);
            $entityManager->flush();

            return $this->redirectToRoute('restrictions_index');
        }

        return $this->render('restrictions/new.html.twig', [
            'restriction' => $restriction,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="restrictions_show", methods={"GET"})
     */
    public function show(Restrictions $restriction): Response
    {
        return $this->render('restrictions/show.html.twig', [
            'restriction' => $restriction,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="restrictions_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Restrictions $restriction): Response
    {
        $form = $this->createForm(RestrictionsType::class, $restriction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('restrictions_index');
        }

        return $this->render('restrictions/edit.html.twig', [
            'restriction' => $restriction,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="restrictions_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Restrictions $restriction): Response
    {
        if ($this->isCsrfTokenValid('delete'.$restriction->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($restriction);
            $entityManager->flush();
        }

        return $this->redirectToRoute('restrictions_index');
    }
}
