<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TrickRepository;
use Knp\Component\Pager\PaginatorInterface;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(TrickRepository $trickRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $tricks = $trickRepository->findBy([], ['created_at' => 'DESC']);

        if (empty($tricks)) {
            $this->addFlash('danger', 'Il n\'y a pas de figures disponibles.');
        }

        $tricks = $paginator->paginate(
            $tricks,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render(
            'home/index.html.twig',
            [
                'tricks' => $tricks
            ]
        );
    }
}
