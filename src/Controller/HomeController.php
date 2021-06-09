<?php

namespace App\Controller;

use App\Repository\GroupRepository;
use App\Repository\MessageRepository;
use App\Repository\PictureRepository;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use App\Repository\VideoRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{

  protected $trickRepository;
  protected $messageRepository;
  protected $userRepository;
  protected $groupRepository;
  protected $pictureRepository;
  protected $videoRepository;

  public function __construct(TrickRepository $trickRepository, MessageRepository $messageRepository, UserRepository $userRepository, GroupRepository $groupRepository, PictureRepository $pictureRepository, VideoRepository $videoRepository)
  {
    $this->trickRepository = $trickRepository;
    $this->messageRepository = $messageRepository;
    $this->userRepository = $userRepository;
    $this->groupRepository = $groupRepository;
    $this->pictureRepository = $pictureRepository;
    $this->videoRepository = $videoRepository;
  }

  /**
   * @Route("/", name="homepage"))
   */
  public function homepage()
  {
    return $this->render('home.html.twig');
  }

  /**
   * @Route("/admin", name="admin_home"))
   * @IsGranted("ROLE_ADMIN", message="You have to be authenticated as an admin to see this page")
   */
  public function admin()
  {
    return $this->render('admin/home.html.twig', [
      'tricks' => $this->trickRepository->findAll(),
      'messages' => $this->messageRepository->findAll(),
      'users' => $this->userRepository->findAll(),
      'groups' => $this->groupRepository->findAll(),
      'pictures' => $this->pictureRepository->findAll(),
      'videos' => $this->videoRepository->findAll()
    ]);
  }
}
