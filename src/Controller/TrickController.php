<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\PaginatorBundle\DependencyInjection\Compiler\PaginatorConfigurationPass;
use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickController extends AbstractController
{
    protected $paginator;

    protected $trickRepository;

    public function __construct(TrickRepository $trickRepository, PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
        $this->trickRepository = $trickRepository;
    }

    /**
     * @Route("/tricks/all", name="trick_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('trick/index.html.twig', [
            'tricks' => $this->trickRepository->findAll(),
        ]);
    }

    /**
     * @Route("/tricks/page/{page<\d+>}", name="trick_page", methods={"GET"})
     */
    public function renderPaginatedTricks(int $page = 1, int $limit = 12)
    {
        $data = $this->trickRepository->findBy([], ['updatedAt' => 'DESC']);

        $tricks = $this->paginator->paginate(
            $data,
            $page,
            $limit
        );

        $lastPageNumber = ceil($tricks->getTotalItemCount() / $tricks->getItemNumberPerPage());

        $isLastPage = false;

        if ($tricks->getCurrentPageNumber() >= $lastPageNumber) {
            $isLastPage = true;
        }
        return $this->render('trick/list.html.twig', [
            'tricks' => $tricks,
            'isLastPage' => $isLastPage
        ]);
    }

    /**
     * @Route("/trick/new", name="trick_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setCreatedAt(new \DateTime());
            $trick->setUpdatedAt($trick->getCreatedAt());
            $trick->setSlug((new Slugify())->slugify($trick->getName()));

            $em->persist($trick);
            $em->flush();

            return $this->redirectToRoute('trick_show', [
                'group_slug' => $trick->getTrickGroup()->getSlug(),
                'slug' => $trick->getSlug()
            ]);
        }

        return $this->render('trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{group_slug}/trick/{slug}", name="trick_show", methods={"GET"})
     */
    public function show(Trick $trick): Response
    {
        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
        ]);
    }

    /**
     * @Route("/trick/{id<\d+>}/edit", name="trick_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Trick $trick, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setUpdatedAt(new \DateTime());
            $trick->setSlug((new Slugify())->slugify($trick->getName()));

            $em->flush();

            return $this->redirectToRoute('trick_show', [
                'group_slug' => $trick->getTrickGroup()->getSlug(),
                'slug' => $trick->getSlug()
            ]);
        }

        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/trick/{id}", name="trick_delete", methods={"POST"})
     */
    public function delete(Request $request, Trick $trick): Response
    {
        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($trick);
            $entityManager->flush();
        }

        return $this->redirect(
            $this->generateUrl('homepage') . '#main-content'
        );
    }
}
