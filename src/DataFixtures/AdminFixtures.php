<?php

namespace App\DataFixtures;

use app\entity\AdminUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



class AdminFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {

        for($i=0; $i<10; $i++) {

            $admin = new AdminUser();
            $admin->setEmail("jt.judo$i@mail.ru");
            $admin->setRoles(['ROLE_ADMIN']);
            $admin->setPassword($this->passwordEncoder->encodePassword(
                $admin,
                'admin'
            ));
            $manager->persist($admin);
        }

        $manager->flush();
    }
}
