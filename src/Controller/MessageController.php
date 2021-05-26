<?php

namespace App\Controller;

use App\Entity\Message;
use App\Pagination\PaginationService;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MessageController extends AbstractController
{
    protected $messageRepository;

    public function __construct(MessageRepository $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    /**
     * @Route("/message", name="message")
     */
    public function index(): Response
    {
        return $this->render('message/index.html.twig', [
            'controller_name' => 'MessageController',
        ]);
    }

    /**
     * @Route("/message/{trickId<\d+>}/page/{page<\d+>}", name="message_page", methods={"GET"})
     * @Route("/message/{trickId<\d+>}/page/{page<\d+>}/{limit}-per-page", name="message_page_with_limit", methods={"GET"})
     */
    public function renderPaginatedMessages(int $trickId, int $page = 1, int $limit = 10, PaginationService $pagination)
    {
        $criteria = ['trick' => $trickId];
        $orderBy = ['createdAt' => 'DESC'];

        $options = $pagination->getRenderOptions('messages', $this->messageRepository, $criteria, $orderBy, $limit, $page);

        $options['trickId'] = $trickId;

        return $this->render('message/list.html.twig', $options);
    }
}