<?php

namespace App\DataFixtures;

use App\Entity\Enrollments;
use App\Entity\Lessons;
use App\Entity\Progress;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProgressFixture extends Fixture
{
    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        // Assuming you have Enrollments and Lessons loaded previously

        $enrollments = $manager->getRepository(Enrollments::class)->findAll();
        $lessons = $manager->getRepository(Lessons::class)->findAll();

        foreach ($enrollments as $enrollment) {
            foreach ($lessons as $lesson) {
                // Create progress records for each combination of enrollment and lesson
                $progress = new Progress();
                $progress->setStatus(random_int(0, 1));
                $progress->setCourseStatus(random_int(0, 1) ? 'incomplete' : 'completed');
                $progress->setLastAccessed(new \DateTimeImmutable());
                $progress->setEnrollment($enrollment);
                $progress->setLessons($lesson);

                $manager->persist($progress);
            }
        }

        $manager->flush();
    }
}
