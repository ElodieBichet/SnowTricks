<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{

  /**
   * @Route("/", name="homepage"))
   */
  public function homepage()
  {
    return $this->render('home.html.twig');
  }
}
