<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/all", name="user")
     * @IsGranted("ROLE_ADMIN", message="You have to be authenticated as an admin to see this page")
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $this->userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"POST"})
     * @IsGranted("USER_DELETE", subject="user", message="You can only delete your own profile")
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', 'The user has been successfully removed.');
        }

        return $this->redirect(
            $this->generateUrl('homepage') . '#main-content'
        );
    }
}
