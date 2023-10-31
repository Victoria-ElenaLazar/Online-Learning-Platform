<?php

namespace App\Controller;

use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(): Response
    {
        $user = $this->getUser();
        return $this->render('user/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/all-users', name: 'app_all_users')]
    public function users(UsersRepository $usersRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_INSTRUCTOR');
        $users = $usersRepository->findAll();
        return $this->render('all-users/all-index.html.twig', [
            'users' => $users,
        ]);
    }
}
