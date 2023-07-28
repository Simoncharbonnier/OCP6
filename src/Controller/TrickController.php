<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TrickRepository;
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

    public function getSortedComments()
    {}
}
