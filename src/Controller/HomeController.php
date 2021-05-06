<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{

  /**
   * @Route("/", name="homepage")
   */
  public function homepage(TrickRepository $tricksRepository, Request $request, PaginatorInterface $paginator)
  {
    $data = $tricksRepository->findBy([], ['updatedAt' => 'DESC']);

    $tricks = $paginator->paginate(
      $data,
      $request->query->getInt('page', 1),
      12
    );

    return $this->render('home.html.twig', [
      'tricks' => $tricks
    ]);
  }
}
