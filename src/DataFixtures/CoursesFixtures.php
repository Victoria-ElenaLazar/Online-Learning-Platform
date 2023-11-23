<?php

namespace App\DataFixtures;

use App\Entity\Courses;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class CoursesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        global $i;
        global $user;
        if ($i % 2 == 0) {
            $courses = new Courses();
            $courses->setInstructor($user);
            $courses->setTitle('Course title ' . $i);
            $courses->setDescription('Course description ' . $i);
            $courses->setHours($i);
            $courses->setMinutes($i);
            $manager->persist($courses);
            $manager->flush();

        }
    }
}
