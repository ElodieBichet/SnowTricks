<?php

namespace App\Controller;

use App\Entity\Message;
use App\Service\PaginationService;
use App\Repository\MessageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     * @Route("/admin", name="message_admin")
     * @IsGranted("ROLE_ADMIN", message="You have to be authenticated as an admin to see this page")
     */
    public function index(): Response
    {
        return $this->render('admin/message/index.html.twig', [
            'messages' => $this->messageRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{trickId<\d+>}/page/{page<\d+>}", name="message_page", methods={"GET"})
     * @Route("/{trickId<\d+>}/page/{page<\d+>}/{limit}-per-page", name="message_page_with_limit", methods={"GET"})
     */
    public function renderPaginatedMessages(int $trickId, int $page = 1, int $limit = 10, PaginationService $pagination)
    {
        $queryBuilder = $this->messageRepository->createQueryBuilder('item')
            ->where("item.trick = $trickId")
            ->orderBy('item.createdAt', 'DESC');

        $options = $pagination->getRenderOptions('messages', $queryBuilder, $limit, $page);

        $options['trickId'] = $trickId;

        return $this->render('message/list.html.twig', $options);
    }

    /**
     * @Route("/{id}", name="message_delete", methods={"POST"})
     * @IsGranted("MESSAGE_DELETE", subject="message", message="You can only delete your own messages")
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
