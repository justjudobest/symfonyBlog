<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\StaticPages;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\ModerationRepository;
use App\Repository\PostRepository;
use App\Repository\RestrictionsRepository;
use App\Repository\StaticPagesRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param Request $request
     * @param PostRepository $postRepository
     * @param RestrictionsRepository $restrictionsRepository
     * @param MailerInterface $mailer
     * @return Response
     */
    public function index(
        Request $request,
        PostRepository $postRepository,
        RestrictionsRepository $restrictionsRepository,
        UserRepository $userRepository,
        StaticPagesRepository $staticPagesRepository
    ): Response
    {
        $staticPages = $staticPagesRepository->findAll();
        $currentUser = $this->getUser();
        $arrayBanList = $restrictionsRepository->findBy(['user' => $currentUser]);
        $dateToday = (new \DateTime())->format('Y-m-d');

            foreach ($arrayBanList as $objectBanList) {
                    if(strtotime($dateToday) < strtotime($objectBanList->getFinish()->format("m.d.y"))) {
                        return $this->redirectToRoute('app_logout');
                    }
            }

        $limit = 2;

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

        return $this->render('home/index.html.twig', [
            'posts' => $post,
            'sort' => $sort,
            'previous' => $page - 1,
            'next' => $page + 1,
            'offset' => $offset,
            'limit' => $limit,
            'paged' => $page,
            'user' => $currentUser,
            'staticPages' =>  $staticPages
        ]);
    }

    /**
     * @Route("/post/{id}", name="singlePost", methods={"GET"})
     */
    public function singlePost(
        Post $post,
        CommentRepository $commentRepository,
        StaticPagesRepository $staticPagesRepository
    )
    {
        $staticPages = $staticPagesRepository->findAll();

        $arrayNoParent = $commentRepository->findNoParent();
        $countComment = $commentRepository->findBy(['PreModeration' => 1]);
        $arrayNotRegistered = $commentRepository->findBy(['notRegistered' => 1]);
        $notRegistered = 0;
        $activ = 0;

            foreach ($arrayNotRegistered as $objectNotRegistered) {
                $notRegistered = $objectNotRegistered->getNotRegistered();
            }


        $arrayComments = $commentRepository->findAll();

            foreach ($arrayComments as $objectCommetns) {
                $activ = $objectCommetns->getActiv();
            }




        return $this->render('home/singlePost.html.twig',[
            'post' => $post,
            'activ' => $activ,
            'comments' => $arrayComments,
            'commentNoParent' => $arrayNoParent,
            'countComment' => $countComment,
            'notRegistered' => $notRegistered,
            'staticPages' => $staticPages,
        ]);
    }

    /**
     * @Route("/subscription", name="subscription", methods={"POST"})
     * @param User $user
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function subscription(Request $request, UserRepository $userRepository)
    {

        $currentUser = $request->getSession()->get('_security.last_username');
        $arrayUser = $userRepository->findAll();

        foreach ($arrayUser as $objectUser) {
            if ($currentUser == $objectUser->getUsername()) {
                if ($objectUser->getSubscription() == 0) {
                    $userSubscription = $objectUser->setSubscription('1');
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($userSubscription);

                } else {
                    $userSubscription = $objectUser->setSubscription('0');
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($userSubscription);
                }
            }

        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        return $this->redirectToRoute('home');
    }


    /**
     * @Route("/addToFavorites/{post}", name="addToFavorites", methods={"POST","GET"})
     * @param Request $request
     * @param UserRepository $userRepository
     */
    public function addToFavorites(
        Post $post,
        Request $request,
        UserRepository $userRepository,
        PostRepository $postRepository
    )
    {
        $currentUser = $request->getSession()->get('_security.last_username');
        $arrayUser = $userRepository->findAll();
        $currentId = $request->get('post-id');
        $arrayPost = $postRepository->findAll();
        $entityManager = $this->getDoctrine()->getManager();

        foreach ($arrayUser as $objectUser) {
            if ($currentUser == $objectUser->getUsername()) {
                $userId = $objectUser;
            }
        }

        foreach ($arrayPost as $objectPost) {
            if ($currentId == $objectPost->getId()) {
                $objectPost->addUser($userId);
                $entityManager->persist($objectPost);
                $entityManager->flush();
            }
        }

        return $this->redirectToRoute('singlePost',['id' => $post->getId()]);

    }

    /**
     * @Route("/edituser/{id}", name="user_user_edit", methods={"GET","POST"})
     */
    public function editUser(User $user, Request $request): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();
            $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit_user.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/favorites/{id}", name="favorites", methods={"GET","POST"})
     */
    public function favorites(User $user, Request $request): Response
    {


        return $this->render('favorites/favorites.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/removeFavorites", name="removeFavorites", methods={"GET","POST"})
     */
    public function delete(Request $request, UserRepository $userRepository): Response
    {
        $arrayUser = $userRepository->findAll();
        $currentUser = $this->getUser();
        $postId = $request->get('post-id');

        foreach ($arrayUser as $objectUser) {

            if ($currentUser->getUsername() == $objectUser->getUsername()) {
                foreach ($objectUser->getPosts() as $posts) {
                    if ($posts->getId() == $postId) {

                        $entityManager = $this->getDoctrine()->getManager();
                        $objectUser->removePost($posts);
                        $entityManager->flush();
                    }
                }
            }
        }
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/new/{post}", name="comment_new_user", methods={"GET","POST"})
     */
    public function new(
        Post $post,
        Request $request,
        ModerationRepository $moderationRepository,
        CommentRepository $commentRepository
    ): Response
    {
        $value = $moderationRepository->find(['id' => 1]);
        $arrayComments = $commentRepository->findAll();

        $comment = new Comment();

        $text = $request->get('message');
        $name = $request->get('contact-name');

        if ($text == '' || $name == '' || strlen($text) > 3000) {
            return $this->redirectToRoute('singlePost',['id' => $post->getId()]);
        }
        if ($value->getValue() == 0) {
            $comment->setPreModeration('1');
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
        } else {
            $comment->setPreModeration('0');
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
        }
        foreach ($arrayComments as $objectComment) {
            if ($objectComment->getNotRegistered() == 0) {
                $comment->setNotRegistered('0');
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($comment);
            } else {
                $comment->setNotRegistered('1');
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($comment);
            }

        }

        $date = new \DateTime();
        $comment->setCreated($date);
        $comment->setText($text);
        $comment->setSenderName($name);
        $comment->setActiv(True);
        $comment->setPost($post);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($comment);
        $entityManager->flush();

        return $this->redirectToRoute('singlePost',['id' => $post->getId()]);
    }

    /**
     * @Route("/page-{title}", name="staticPage", methods={"GET"})
     */
    public function StaticPage(StaticPages $staticPage, StaticPagesRepository $staticPagesRepository)
    {
        $staticPages = $staticPagesRepository->findAll();

        return $this->render('home/staticPage.html.twig',[
            'staticPages' => $staticPages,
            'staticPage' => $staticPage


        ]);
    }

    /**
     * @Route("/categories", name="categories", methods={"GET","POST"})
     */
    public function pageCategories(
        CategoryRepository $categoryRepository,
        StaticPagesRepository $staticPagesRepository,
        Request $request,
        PostRepository $postRepository
    ): Response
    {
        $staticPages = $staticPagesRepository->findAll();


        $limit = 2;
        $offset = max(0, $request->query->getInt('paged'));
        $page = 1;

        if ($offset >= 1) {
            $page = $offset;
            $offset = $offset * $limit - $limit;
        }

        $categories = $categoryRepository->PaginationCategories($limit,$offset);

        return $this->render('home/categories.html.twig',[
            'staticPages' => $staticPages,
            'categories' => $categories,
            'previous' => $page - 1,
            'next' => $page + 1,
            'offset' => $offset,
            'limit' => $limit,
            'paged' => $page,
        ]);

    }

    /**
     * @Route("/categories", name="postsAll", methods={"GET","POST"})
     */
    public function pagePosts(
        CategoryRepository $categoryRepository,
        StaticPagesRepository $staticPagesRepository,
        Request $request,
        PostRepository $postRepository
    ): Response
    {
        $staticPages = $staticPagesRepository->findAll();
        $limit = 2;
        $offset = max(0, $request->query->getInt('paged'));
        $page = 1;
        if ($offset >= 1) {
            $page = $offset;
            $offset = $offset * $limit - $limit;
        }

        $categories = $categoryRepository->PaginationCategories($limit,$offset);

        return $this->render('home/categories.html.twig',[
            'staticPages' => $staticPages,
            'categories' => $categories,
            'previous' => $page - 1,
            'next' => $page + 1,
            'offset' => $offset,
            'limit' => $limit,
            'paged' => $page,
        ]);

    }

    /**
     * @Route("/categories/{name}", name="categoriesPosts", methods={"GET","POST"})
     */
    public function categoriesPosts(
        Category $category,
        StaticPagesRepository $staticPagesRepository,
        Request $request,
        CategoryRepository $categoryRepository

    )
    {
        $staticPages = $staticPagesRepository->findAll();
        $limit = 2;
        $offset = max(0, $request->query->getInt('paged'));
        $page = 1;
        if ($offset >= 1) {
            $page = $offset;
            $offset = $offset * $limit - $limit;
        }

        $categoryId = $category->getId();
        $category = $categoryRepository->PaginationCategoriesPosts($limit, $offset, $categoryId);

        return $this->render('home/categoriesPosts.html.twig',[
            'categories' => $category,
            'staticPages' => $staticPages,
            'previous' => $page - 1,
            'next' => $page + 1,
            'offset' => $offset,
            'limit' => $limit,
            'paged' => $page,

        ]);

    }

}


