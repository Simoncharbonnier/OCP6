<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Form\ForgotPasswordFormType;
use App\Form\ResetPasswordFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class SecurityController extends AbstractController
{
    /**
     * Login
     * @param AuthenticationUtils $authenticationUtils authentication utils
     *
     * @return Response
     */
    #[Route(path: '/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * Logout
     * @return void
     * @throws LogicException
     */
    #[Route(path: '/déconnexion', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * Forgot password page or send email to reset password
     * @param Request $request request
     * @param UserRepository $userRepository user repository
     * @param TokenGeneratorInterface $tokenGenerator token generator
     * @param EntityManagerInterface $entityManager entity manager
     * @param EmailVerifier $emailVerifier email verifier
     *
     * @return Response
     */
    #[Route('/mot-de-passe-oublié', name:'app_forgot')]
    public function forgottenPassword(
        Request $request,
        UserRepository $userRepository,
        TokenGeneratorInterface $tokenGenerator,
        EntityManagerInterface $entityManager,
        EmailVerifier $emailVerifier
    ): Response
    {
        $form = $this->createForm(ForgotPasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $user = $userRepository->findOneBy(['username' => $form->get('username')->getData()]);

            if ($user) {
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $entityManager->persist($user);
                $entityManager->flush();

                $url = $this->generateUrl('app_reset', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                $emailVerifier->sendEmailResetPassword($url, $user,
                    (new TemplatedEmail())
                        ->from(new Address('simoncharbonnier.blog@gmail.com', 'SnowTricks Bot'))
                        ->to($user->getMail())
                        ->subject('Réinitialisation du mot de passe')
                        ->htmlTemplate('mail/reset_password_email.html.twig')
                );

                $this->addFlash('success', 'Email envoyé avec succès.');
                return $this->redirectToRoute('app_login');
            }

            $this->addFlash('danger', 'L\'utilisateur n\'existe pas.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/forgot_password.html.twig', [
            'forgotPasswordForm' => $form->createView()
        ]);
    }

    /**
     * Reset password
     * @param string $token reset token
     * @param Request $request request
     * @param UserRepository $userRepository user repository
     * @param EntityManagerInterface $entityManager entity manager
     * @param UserPasswordHasherInterface $passwordHasher password hasher
     *
     * @return Response
     */
    #[Route('/réinitialisation-mot-de-passe/{token}', name:'app_reset')]
    public function resetPassword(
        string $token,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        $user = $userRepository->findOneBy(['reset_token' => $token]);

        if ($user) {
            $form = $this->createForm(ResetPasswordFormType::class);
            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                if ($user->getUsername() !== $form->get('username')->getData()) {
                    $this->addFlash('danger', 'Veuillez vérifier le nom d\'utilisateur.');
                    return $this->redirectToRoute('app_reset', [ 'token' => $token ]);
                }

                if (strlen($form->get('password')->getData()) < 6) {
                    $this->addFlash('danger', 'Le mot de passe doit faire au moins 6 caractères.');
                    return $this->redirectToRoute('app_reset', [ 'token' => $token ]);
                }

                $user->setResetToken(null);
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Mot de passe mis à jour avec succès.');
                return $this->redirectToRoute('app_home');
            }

            return $this->render('security/reset_password.html.twig', [
                'resetPasswordForm' => $form->createView()
            ]);
        }

        $this->addFlash('danger', 'Jeton invalide.');
        return $this->redirectToRoute('app_home');
    }
}
