<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Entity\Users;
use App\Form\ProfileType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(): Response
    {
        /** @var Users $user */
        $user = $this->getUser();
        return $this->render('profile/index.html.twig', [
            'form' => $this->createForm(ProfileType::class),
            'user' => $user,
            'image_path' => $user->getProfile()?->getImage(),
            'title' => 'Profile of <br>' . $user->getUsername()
        ]);
    }

    #[Route('/profile/{id}/edit', name: 'app_profile_edit')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function edit(Request $request, SluggerInterface $slugger,
                         Profile $profile, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

                // Move the file to the directory where pictures are stored
                try {
                    $image->move(
                        $this->getParameter('app.profile_images'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    echo "Something went wrong. Please try again!" . $e->getMessage();
                }

                // updates the 'pictureFilename' property to store the file name
                // instead of its contents
                $profile->setImage($newFilename);
            }

            $entityManager->persist($profile);
            $entityManager->flush();

            return $this->redirectToRoute('app_profile');
        }

        /** @var Users $user */
        $user = $this->getUser();
        return $this->render('profile/edit.html.twig', [
            'user' => $user,
            'image_path' => $user->getProfile()?->getImage(),
            'form' => $form,
            'title' => 'Profile of <br>' . $user->getUsername()
        ]);
    }

    #[Route('/profile/new', name: 'app_profile_new')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function new(Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {
        $profile = new Profile();
        $profile->setUsers($this->getUser());

        $form = $this->createForm(ProfileType::class, $profile);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

                // Move the file to the directory where pictures are stored
                try {
                    $image->move(
                        $this->getParameter('app.profile_images'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    echo $e->getMessage();
                }

                // updates the 'pictureFilename' property to store the file name
                // instead of its contents
                $profile->setImage($newFilename);
            }

            $entityManager->persist($profile);
            $entityManager->flush();

            return $this->redirectToRoute('app_profile');
        }

        /** @var Users $user */
        $user = $this->getUser();
        return $this->render('profile/new.html.twig', [
            'user' => $user,
            'form' => $form,
            'title' => 'Profile of <br>' . $user->getUsername()
        ]);
    }
}
