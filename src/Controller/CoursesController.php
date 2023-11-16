<?php

namespace App\Controller;

use App\Entity\Courses;
use App\Entity\Enrollments;
use App\Entity\Lessons;
use App\Entity\Progress;
use App\Entity\Users;
use App\Form\CoursesType;
use App\Repository\CoursesRepository;
use App\Repository\EnrollmentsRepository;
use App\Repository\LessonsRepository;
use App\Repository\ProgressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\MakerBundle\Tests\tmp\current_project_xml\src\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/courses')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class CoursesController extends AbstractController
{
    #[Route('/', name: 'app_courses_index', methods: ['GET'])]
    public function index(CoursesRepository $coursesRepository): Response
    {
        /** @var Users $currentUser */
        $currentUser = $this->getUser();
        return $this->render('courses/index.html.twig', [
            'courses' => $coursesRepository->findAll(),
            'user' => $currentUser,
            'title' => 'List of courses'
        ]);
    }

    #[Route('/new', name: 'app_courses_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $course = new Courses();
        $form = $this->createForm(CoursesType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($course);
            $entityManager->flush();

            return $this->redirectToRoute('app_courses_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('courses/new.html.twig', [
            'course' => $course,
            'form' => $form,
            'title' => 'Create a new course'
        ]);
    }

    #[Route('/{id}', name: 'app_courses_show', methods: ['GET'])]
    public function show(Courses $course): Response
    {
        return $this->render('courses/show.html.twig', [
            'course' => $course,
            'title' => 'Course details <br>"' . $course->getTitle() . '"'
        ]);
    }

    #[Route('/{id}/edit', name: 'app_courses_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Courses $course, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CoursesType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_courses_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('courses/edit.html.twig', [
            'course' => $course,
            'form' => $form,
            'title' => 'Edit your course <br>' . $course->getId()
        ]);
    }

    #[Route('/{id}', name: 'app_courses_delete', methods: ['POST'])]
    public function delete(Request $request, Courses $course, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $course->getId(), $request->request->get('_token'))) {
            $entityManager->remove($course);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_courses_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/enroll', name: 'app_courses_enroll', methods: ['GET'])]
    public function enroll(Request $request, Courses $course, EntityManagerInterface $entityManager): Response
    {
        $enrollment = new Enrollments();
        $enrollment->setCourse($course);
        $enrollment->setUser($this->getUser());
        $entityManager->persist($enrollment);
        $entityManager->flush();
        return $this->redirectToRoute('app_courses_show', ['id' => $request->get('id')]);
    }

    #[Route('/{id}/unenroll', name: 'app_courses_unenroll', methods: ['GET'])]
    public function unenroll(Courses $course, EntityManagerInterface $entityManager, EnrollmentsRepository $enrollmentsRepository): Response
    {
        $user = $this->getUser();
        $enrollment = $enrollmentsRepository->findOneBy(['course' => $course, 'user' => $user]);

        if ($enrollment) {
            $progressRecords = $enrollment->getProgresses();
            foreach ($progressRecords as $progress) {
                $entityManager->remove($progress);
            }

            $entityManager->remove($enrollment);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_courses_index');
    }

    #[Route('/{id}/complete', name: 'app_course_complete', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function complete(Courses $course, ProgressRepository $progressRepository, EnrollmentsRepository $enrollmentsRepository,
                             EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $enrollment = $enrollmentsRepository->findOneBy(['course' => $course, 'user' => $user]);
        $enrollments = $enrollmentsRepository->findOneBy(['course' => $course, 'user' => $user]);

        $progress = $progressRepository->findOneBy(['courses' => $course]);

        if ($progress && $progress->getStatus() == 1) {
            $progress->setCourses($course);
            $progress->setCourseStatus('completed');
            $progress->setLastAccessed(new \DateTimeImmutable());
            $progress->setEnrollment($enrollments);
        }


        $enrollment->setUser($user);

        $entityManager->persist($progress);
        $entityManager->flush();

        return $this->redirectToRoute('app_courses_index', ['id' => $enrollment->getCourse()->getId(),]);

    }
}
