<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TrickRepository;
use App\Form\TrickFormType;
use App\Form\CommentFormType;
use App\Entity\Trick;
use App\Entity\Comment;
use Monolog\DateTimeImmutable;
use Knp\Component\Pager\PaginatorInterface;

class TrickController extends AbstractController
{
    #[Route('/figure/{slug}', name: 'app_trick')]
    public function index(
        string $slug,
        TrickRepository $trickRepository,
        EntityManagerInterface $entityManager,
        PaginatorInterface $paginator,
        Request $request
    ): Response
    {
        $trick = $trickRepository->findBy(['slug' => $slug]);

        if (empty($trick)) {
            $this->addFlash('danger', 'Cette figure n\'existe pas.');
            return $this->redirectToRoute('app_home');
        }
        $trick = $trick[0];

        $form = $this->createForm(CommentFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = new Comment();
            $comment->setMessage($form->get('message')->getData());
            $comment->setUser($this->getUser());
            $comment->setTrick($trick);
            $comment->setCreatedAt(new DateTimeImmutable('now'));

            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Commentaire ajouté.');

            $trick->addComment($comment);
            $form = $this->createForm(CommentFormType::class);
        }

        $comments = $paginator->paginate(
            $trick->getSortedComments(),
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('trick/index.html.twig', [
            'trick' => $trick,
            'commentForm' => $form->createView(),
            'comments' => $comments
        ]);
    }

    #[Route('/ajouter-figure', name: 'app_trick_new')]
    public function new(
        EntityManagerInterface $entityManager,
        Request $request
    ): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $trick = new Trick();
        $form = $this->createForm(TrickFormType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setUser($this->getUser());
            $trick->setCreatedAt(new DateTimeImmutable('now'));
            $trick->setUpdatedAt(new DateTimeImmutable('now'));

            if ($trick->getImages()) {
                $counter = 0;
                foreach ($trick->getImages() as $image) {
                    $image->setTrick($trick);
                    $imageName = $trick->getName().'-'.$counter.'.jpg';

                    if (move_uploaded_file($image->getName(), 'assets/img/tricks/'.$imageName) === TRUE) {
                        $image->setName($imageName);
                        $entityManager->persist($image);
                        $counter++;
                    } else {
                        $trick->removeImage($image);
                    }
                }
            }

            if ($trick->getVideos()) {
                foreach ($trick->getVideos() as $video) {
                    $video->setTrick($trick);
                    if ($video->getName()) {
                        $entityManager->persist($video);
                    } else {
                        $trick->removeVideo($video);
                    }
                }
            }

            $entityManager->persist($trick);
            $entityManager->flush();

            $this->addFlash('success', 'Figure ajoutée.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('trick/new.html.twig', [
            'trickForm' => $form->createView()
        ]);
    }

    #[Route('/modifier-figure/{slug}', name: 'app_trick_edit')]
    public function edit(
        string $slug,
        TrickRepository $trickRepository,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response
    {
        $trick = $trickRepository->findBy(['slug' => $slug]);
        if (empty($trick)) {
            $this->addFlash('danger', 'Cette figure n\'existe pas.');
            return $this->redirectToRoute('app_home');
        }
        $trick = $trick[0];

        if (!$this->getUser() || $this->getUser() !== $trick->getUser()) {
            $this->addFlash('danger', 'Vous n\'avez pas les permissions.');
            return $this->redirectToRoute('app_trick', [ 'slug' => $slug ]);
        }

        $form = $this->createForm(TrickFormType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dump($trick);die;
            $trick->setUpdatedAt(new DateTimeImmutable('now'));

            $entityManager->persist($trick);
            $entityManager->flush();

            $this->addFlash('success', 'Figure modifiée.');
            return $this->redirectToRoute('app_trick', [ 'slug' => $slug ]);
        }

        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'trickForm' => $form->createView()
        ]);
    }
}
