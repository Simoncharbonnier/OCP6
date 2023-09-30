<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;

class UserController extends AbstractController
{
    #[Route('/profil/{username}', name: 'app_user')]
    public function index(
        string $username,
        UserRepository $userRepository
    ): Response
    {
        $user = $userRepository->findBy(['username' => $username]);

        if (empty($user)) {
            $this->addFlash('danger', 'Cet utilisateur n\'existe pas.');
            return $this->redirectToRoute('app_home');
        }
        $user = $user[0];

        return $this->render('user/index.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/modifier-profil/{username}', name: 'app_user_edit')]
    public function edit(
        string $username,
        UserRepository $userRepository,
        Request $request
    ): Response
    {
        $user = $userRepository->findBy(['username' => $username]);

        if (empty($user)) {
            $this->addFlash('danger', 'Cet utilisateur n\'existe pas.');
            return $this->redirectToRoute('app_home');
        }
        $user = $user[0];

        if (!$this->getUser() || $this->getUser() !== $user) {
            $this->addFlash('danger', 'Vous n\'avez pas les permissions.');
            return $this->redirectToRoute('app_user', [ 'username' => $username ]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user
        ]);
    }
}
