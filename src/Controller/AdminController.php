<?php

namespace App\Controller;
use App\Repository\CategoryRepository;
use App\Repository\ImageRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(Request $request, CategoryRepository $categoryRepository): Response
    {


        return $this->render('admin/admin.html.twig', [

        ]);

    }
}
