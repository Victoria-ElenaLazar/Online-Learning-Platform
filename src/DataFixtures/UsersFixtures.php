<?php

namespace App\DataFixtures;

use App\Entity\Users;
use App\Entity\Profile;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



class UsersFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 21; $i < 40; $i++) {
            $user = new Users();
            $user->setUsername('user' . $i);
            $user->setEmail('user' . $i . '@gmail.com');
            $user->setPassword(
                $this->hasher->hashPassword(
                    $user,
                    '123456'
                )
            );
            if ($i % 2 == 0) {
                $user->setRoles(['ROLE_INSTRUCTOR']);
            } else {
                $user->setRoles(['ROLE_STUDENTS']);
            }
            $manager->persist($user);
            $manager->flush();

            $profile = new Profile();
            $profile->setUsers($user);
            $profile->setCountry('USA');
            $profile->setAddress('Test address ' . $i);
            $manager->persist($profile);
            $manager->flush();
        }
    }
}
