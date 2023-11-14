<?php

namespace App\DataFixtures;

use App\Entity\Lessons;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class LessonsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        global $courses;
        for ($j = 0; $j < 3; $j++) {
            $lesson = new Lessons();
            $lesson->setCourse($courses);
            $lesson->setTitle('Lesson title ' . $j);
            $lesson->setContent('Lesson content ' . $j);
            $manager->persist($lesson);
            $manager->flush();
        }
    }
}
