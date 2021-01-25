<?php


namespace App\Events;


use App\Entity\Post;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class mailerNewPost
{
    public function __construct(MailerInterface $mailer, UserRepository $userRepository, PostRepository $postRepository)
    {
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
        $this->postRepository =$postRepository;
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof Post) {
            return;
        }
        $arrayPost = $this->postRepository->findAll();
        foreach ($arrayPost as $objectPost){
                $postActive = $objectPost->getActiv();
        }
        $arrayUser =  $this->userRepository->findAll();
        foreach ($arrayUser as $objectUser) {
            if ($objectUser->getSubscription() == 1 and $postActive == 1) {

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
                $this->mailer->send($email);
            }
        }
    }


}