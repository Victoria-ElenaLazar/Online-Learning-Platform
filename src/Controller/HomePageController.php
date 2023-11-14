<?php

namespace App\Controller;

use App\Entity\Courses;
use App\Entity\Users;
use App\Repository\CoursesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends AbstractController
{
    #[Route('/', name: 'app_homepage', methods: ['GET'])]
    public function index(CoursesRepository $coursesRepository): Response
    {
        return $this->render('home_page/index.html.twig', [
            'title' => 'E-Learning Platform',
            'courses' => $coursesRepository->findAll()
        ]);
    }
    #[Route('/courses/{id}', name: 'app_courses_show', methods: ['GET'])]
    public function show(Courses $course): Response
    {
        return $this->render('courses/show.html.twig', [
            'course' => $course,
            'title' => 'Course details <br>"' . $course->getTitle() . '"'
        ]);
    }
}
