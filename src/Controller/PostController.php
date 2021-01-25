<?php

namespace App\Controller;

use App\Repository\AdminUserRepository;
use App\Repository\UserRepository;
use App\Service\postExport;
use App\Service\postImport;
use App\Entity\Post;
use App\Form\PostType;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\File;
use App\Service\FileUploader;




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
        $limit = 5;
        $offset = max(0, $request->query->getInt('paged'));
        $page = 1;

        if ($offset >= 1) {
            $page = $offset;
            $offset = $offset * $limit - $limit;
        }

        $sortKey = $request->get('order_key');
        $sort = $request->get('order');
        $searchPost = $request->get('search-post', '');
//        $searchSubHeadline = $request->get('search-subheadline','');
//        $searchSubHeadline = $postRepository->searchsubheadline($searchSubHeadline);
        $post = $postRepository->searchPost($searchPost, $sort, $sortKey, $offset,$limit);

        if ($page > 1 && $page > $paged = ceil($post->count() / $limit)) {
            return $this->redirectToRoute('post_index', ['paged' => $paged]);
        }
        return $this->render('post/index.html.twig', [
            'posts' => $post,
            'sort' => $sort,
            'previous' => $page - 1,
            'next' => $page + 1,
            'offset' => $offset,
            'limit' => $limit,
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
            $file = $post->getImage();
            $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();

            if (!file_exists("upload/gallery/$fileName")) {
                $file->move(
                    $this->getParameter('gallery_upload'),
                    $fileName
                );
                $post->setImage($fileName);

            } else {

                $newFileName = $fileName;
                while ($newFileName == $fileName) {
                    $newFileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();
                }
                $file->move(
                    $this->getParameter('gallery_upload'),
                    $newFileName
                );
                $post->setImage($newFileName);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();
            $file = $post->getImage();
            $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();

            if (!file_exists("upload/gallery/$fileName")) {
                $file->move(
                    $this->getParameter('gallery_upload'),
                    $fileName
                );
                $post->setImage($fileName);

            } else {

                $newFileName = $fileName;
                while ($newFileName == $fileName) {
                    $newFileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();
                }
                $file->move(
                    $this->getParameter('gallery_upload'),
                    $newFileName
                );
                $post->setImage($newFileName);
            }
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
     * @param Request $request
     * @param FileUploader $fileUploader
     * @param Post $post
     * @param UserRepository $userRepository
     * @param MailerInterface $mailer
     * @param $form
     * @return Response
     */
    public function edit(Request $request, FileUploader $fileUploader, Post $post, UserRepository $userRepository, MailerInterface $mailer, $form): Response
    {

        $image = $form->get('image')->getData();
        if ($image) {
            $imageName = $fileUploader->upload($image);
            $post->setImage($imageName);
        }

        $form = $this->createForm(PostType::class, $post);
                $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $arrayUser =  $userRepository->findAll();

            if ($form->get('activ')->getData() == True) {
                foreach ($arrayUser as $objectUser) {
                    if ($objectUser->getSubscription() == 1) {

                        $email = (new Email())
                            ->from('jt.judo@mail.ru')
                            ->to($objectUser->getEmail())
//            //->cc('cc@example.com')
//            //->bcc('bcc@example.com')
//            //->replyTo('fabien@example.com')
//            //->priority(Email::PRIORITY_HIGH)
                            ->subject('new post!')
                            ->text('Sending emails is fun again!')
                            ->html('<p>new post again !</p>');
                        $mailer->send($email);
                    }
                }

            }
            $file = $post->getImage();
            $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();

            if (!file_exists("upload/gallery/$fileName")) {

                $file->move(
                    $this->getParameter('gallery_upload'),
                    $fileName
                );
                $post->setImage($fileName);

            } else {

                $newFileName = $fileName;
                while ($newFileName == $fileName) {
                    $newFileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();
                }
                $file->move(
                    $this->getParameter('gallery_upload'),
                    $newFileName
                );
                $post->setImage($newFileName);
            }
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

    private function generateUniqueFileName()
    {
        // md5() уменьшает схожесть имён файлов, сгенерированных
        // uniqid(), которые основанный на временных отметках
        return md5(uniqid());
    }


}


