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

/**
 * @Route("/message")
 */
class MessageController extends AbstractController
{
    protected $messageRepository;

    public function __construct(MessageRepository $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    /**
     * @Route("/all", name="message")
     */
    public function index(): Response
    {
        return $this->render('message/index.html.twig', [
            'controller_name' => 'MessageController',
        ]);
    }

    /**
     * @Route("/{trickId<\d+>}/page/{page<\d+>}", name="message_page", methods={"GET"})
     * @Route("/{trickId<\d+>}/page/{page<\d+>}/{limit}-per-page", name="message_page_with_limit", methods={"GET"})
     */
    public function renderPaginatedMessages(int $trickId, int $page = 1, int $limit = 10, PaginationService $pagination)
    {
        $criteria = ['trick' => $trickId];
        $orderBy = ['createdAt' => 'DESC'];

        $options = $pagination->getRenderOptions('messages', $this->messageRepository, $criteria, $orderBy, $limit, $page);

        $options['trickId'] = $trickId;

        return $this->render('message/list.html.twig', $options);
    }

    /**
     * @Route("/{id}", name="message_delete", methods={"POST"})
     */
    public function delete(Request $request, Message $message): Response
    {
        if ($this->isCsrfTokenValid('delete' . $message->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($message);
            $entityManager->flush();

            $this->addFlash('success', 'The message has been successfully removed.');
        }

        $trick = $message->getTrick();

        return $this->redirectToRoute('trick_show', [
            'group_slug' => $trick->getTrickGroup()->getSlug(),
            'slug' => $trick->getSlug()
        ]);
    }
}
