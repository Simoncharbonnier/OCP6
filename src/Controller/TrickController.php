<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TrickRepository;

class TrickController extends AbstractController
{
    #[Route('/figure/{slug}', name: 'app_trick')]
    public function index(string $slug, TrickRepository $trickRepository): Response
    {
        $trick = $trickRepository->findBy(['slug' => $slug]);

        if (empty($trick)) {
            $this->addFlash('danger', 'Cette figure n\'existe pas.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('trick/index.html.twig', [
            'trick' => $trick,
        ]);
    }
}
