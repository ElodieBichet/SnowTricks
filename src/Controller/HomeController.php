<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{

  /**
   * @Route("/", name="homepage")
   */
  public function homepage(TrickRepository $tricksRepository)
  {
    $tricks = $tricksRepository->findBy([], [], 15);

    return $this->render('home.html.twig', [
      'tricks' => $tricks
    ]);
  }
}
