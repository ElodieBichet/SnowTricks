<?php

namespace App\Controller;

use App\Repository\VideoRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/video")
 */
class VideoController extends AbstractController
{
    /**
     * @Route("/admin", name="video_admin")
     * @IsGranted("ROLE_ADMIN", message="You have to be authenticated as an admin to see this page")
     */
    public function index(VideoRepository $videoRepository): Response
    {
        return $this->render('admin/video/index.html.twig', [
            'videos' => $videoRepository->findAll(),
        ]);
    }
}
