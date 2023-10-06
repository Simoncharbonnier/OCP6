<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use App\Form\UserFormType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
        EntityManagerInterface $entityManager,
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher
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

        $issetAvatar = $user->getAvatar() !== null;
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            if (preg_match('/^data:image\/(png|jpg|jpeg);base64,/', $user->getAvatar()) === 1) {
                $avatarData = base64_decode(preg_replace('/^data:image\/(png|jpg|jpeg);base64,/', '', $user->getAvatar()));
                file_put_contents('assets/img/users/'.$user->getId().'.jpg', $avatarData);
                $user->setAvatar($user->getId().'.jpg');
            } else if ($user->getAvatar() === null && $issetAvatar) {
                unlink('assets/img/users/'.$user->getId().'.jpg');
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Profil modifié.');
            return $this->redirectToRoute('app_user', [ 'username' => $user->getUsername() ]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'userForm' => $form->createView()
        ]);
    }
}
