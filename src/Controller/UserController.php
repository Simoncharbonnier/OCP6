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
    /**
     * User details
     * @param string $username username
     * @param UserRepository $userRepository user repository
     *
     * @return Response
     */
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

    /**
     * Edit user
     * @param string $username username
     * @param UserRepository $userRepository user repository
     * @param EntityManagerInterface $entityManager entity manager
     * @param Request $request request
     * @param UserPasswordHasherInterface $userPasswordHasher password hasher
     *
     * @return Response
     */
    #[Route('/modifier-profil/{username}', name: 'app_user_edit')]
    public function edit(
        string $username,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        Request $request,
        UserPasswordHasherInterface $passwordHasher
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
            if (!$passwordHasher->isPasswordValid($user, $form->get('plainPassword')->getData())) {
                $this->addFlash('danger', 'Le mot de passe est invalide.');
                return $this->redirectToRoute('app_user', [ 'username' => $username ]);
            }

            if (preg_match('/^data:image\/(png|jpg|jpeg);base64,/', $user->getAvatar()) === 1) {
                $avatarData = base64_decode(preg_replace('/^data:image\/(png|jpg|jpeg);base64,/', '', $user->getAvatar()));
                file_put_contents('assets/img/users/'.$user->getId().'.jpg', $avatarData);
                $user->setAvatar($user->getId().'.jpg');
            } else if ($user->getAvatar() === null && $issetAvatar) {
                unlink('assets/img/users/'.$user->getId().'.jpg');
            } else if ($user->getAvatar() === 'null') {
                $user->setAvatar(null);
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
