<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Knp\Component\Pager\Event\PaginationEvent;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{

  /**
   * @Route("/", name="homepage")
   */
  public function homepage()
  {
    return $this->render('home.html.twig', []);
  }
}
