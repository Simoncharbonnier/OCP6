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
    /**
     * Trick details
     * @param string $slug trick slug
     * @param TrickRepository $trickRepository trick repository
     * @param EntityManagerInterface $entityManager entity manager
     * @param PaginatorInterface $paginator paginator
     * @param Request $request request
     *
     * @return Response
     */
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

        if ($form->isSubmitted() && $form->isValid() && $this->getUser()) {
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

    /**
     * Add trick
     * @param EntityManagerInterface $entityManager entity manager
     * @param Request $request request
     *
     * @return Response
     */
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
                    $imageName = $trick->getSlug().'-'.$counter.'.jpg';

                    if ($image->getName()) {
                        $imageData = base64_decode(preg_replace('/^data:image\/(png|jpg|jpeg);base64,/', '', $image->getName()));
                        file_put_contents('assets/img/tricks/'.$imageName, $imageData);

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

    /**
     * Edit trick
     * @param string $slug trick slug
     * @param TrickRepository $trickRepository trick repository
     * @param EntityManagerInterface $entityManager entity manager
     * @param Request $request request
     *
     * @return Response
     */
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

        $imageCount = count($trick->getImages());
        $form = $this->createForm(TrickFormType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setUpdatedAt(new DateTimeImmutable('now'));

            if ($trick->getImages()) {
                $newImage = false;
                if ($imageCount === 0) {
                    $imageCount = 1;
                    $newImage = true;
                }

                $newImageCount = 0;
                for ($i = 0; $i <= $imageCount - 1; $i++) {
                    $oldImageName = $trick->getSlug().'-'.$i.'.jpg';
                    $imageName = $trick->getSlug().'-'.$newImageCount.'.jpg';

                    if (isset($trick->getImages()[$i])) {
                        $image = $trick->getImages()[$i];

                        if (preg_match('/^data:image\/(png|jpg|jpeg);base64,/', $image->getName()) === 1) {
                            $imageData = base64_decode(preg_replace('/^data:image\/(png|jpg|jpeg);base64,/', '', $image->getName()));
                            file_put_contents('assets/img/tricks/'.$imageName, $imageData);
                            $image->setName($imageName);
                            if ($newImage) {
                                $image->setTrick($trick);
                            }
                            $entityManager->persist($image);

                            if ($i !== $newImageCount && (file_exists('assets/img/tricks/'.$oldImageName))) {
                                unlink('assets/img/tricks/'.$oldImageName);
                            }
                        } else if ($image->getName() !== $imageName) {
                            rename('assets/img/tricks/'.$image->getName(), 'assets/img/tricks/'.$imageName);
                            $image->setName($imageName);
                            $entityManager->persist($image);
                        }

                        $newImageCount++;
                    } else if (file_exists('assets/img/tricks/'.$oldImageName)) {
                        unlink('assets/img/tricks/'.$oldImageName);
                    }
                }
            }

            if ($trick->getVideos()) {
                foreach ($trick->getVideos() as $video) {
                    $entityManager->persist($video);
                }
            }

            $entityManager->persist($trick);
            $entityManager->flush();

            $this->addFlash('success', 'Figure modifiée.');
            return $this->redirectToRoute('app_trick', [ 'slug' => $trick->getSlug() ]);
        }

        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'trickForm' => $form->createView()
        ]);
    }

    /**
     * Delete trick
     * @param string $slug trick slug
     * @param TrickRepository $trickRepository trick repository
     * @param EntityManagerInterface $entityManager entity manager
     *
     * @return Response
     */
    #[Route('/supprimer-figure/{slug}', name: 'app_trick_delete')]
    public function delete(
        string $slug,
        TrickRepository $trickRepository,
        EntityManagerInterface $entityManager
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

        if ($trick->getImages()) {
            foreach ($trick->getImages() as $image) {
                unlink('assets/img/tricks/'.$image->getName());
            }
        }

        $entityManager->remove($trick);
        $entityManager->flush();

        $this->addFlash('success', 'La figure a été supprimée.');
        return $this->redirectToRoute('app_home');
    }
}
