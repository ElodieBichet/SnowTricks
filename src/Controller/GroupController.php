<?php

namespace App\Controller;

use App\Repository\GroupRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/group")
 */
class GroupController extends AbstractController
{
    /**
     * @Route("/admin", name="group_admin")
     */
    public function index(GroupRepository $groupRepository): Response
    {
        return $this->render('admin/group/index.html.twig', [
            'groups' => $groupRepository->findAll(),
        ]);
    }
}
