<?php

namespace App\DataFixtures;

use App\Entity\Enrollments;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;


class EnrollmentsFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        global $courses;
        global $user;

        $enrollment = new Enrollments();
        $enrollment->setCourse($courses);
        $enrollment->setUser($user);
        $manager->persist($enrollment);
        $manager->flush();
        }
}
