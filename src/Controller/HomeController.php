<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Trick;
use App\Repository\TrickRepository;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(TrickRepository $trickRepository): Response
    {
        $tricks = $trickRepository->findAll();

        if (empty($tricks)) {
            $this->addFlash('danger', 'Il n\'y a pas de figures disponibles.');
        }

        return $this->render('home/index.html.twig', [
            'tricks' => $tricks,
        ]);
    }
}
