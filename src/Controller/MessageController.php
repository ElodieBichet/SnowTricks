<?php

namespace App\Controller;

use App\Repository\MessageRepository;
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
    public function renderPaginatedMessages(int $trickId, int $page = 1, int $limit = 10)
    {
        $offset = (($page - 1) * $limit);
        $nbMessages = count($this->messageRepository->findBy(['trick' => $trickId]));
        $data = $this->messageRepository->findBy(['trick' => $trickId], ['createdAt' => 'DESC'], $limit, $offset);

        $lastPageNumber = ceil($nbMessages / $limit);

        $isLastPage = false;

        if ($page >= $lastPageNumber) {
            $isLastPage = true;
        }

        return $this->render('message/list.html.twig', [
            'messages' => $data,
            'trickId' => $trickId,
            'page' => $page,
            'limit' => $limit,
            'isLastPage' => $isLastPage
        ]);
    }
}
