<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Message;
use App\Entity\Picture;
use App\Form\TrickType;
use App\Form\MessageType;
use Cocur\Slugify\Slugify;
use App\Service\PaginationService;
use App\Repository\TrickRepository;
use App\Service\FileUploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/trick")
 */
class TrickController extends AbstractController
{
    protected $trickRepository;

    public function __construct(TrickRepository $trickRepository)
    {
        $this->trickRepository = $trickRepository;
    }

    /**
     * @Route("/all", name="trick_index", methods={"GET"})
     * @IsGranted("ROLE_ADMIN", message="You have to be authenticated as an admin to see this page")
     */
    public function index(): Response
    {
        return $this->render('trick/index.html.twig', [
            'tricks' => $this->trickRepository->findAll(),
        ]);
    }

    /**
     * @Route("/page/{page<\d+>}", name="trick_page", methods={"GET"})
     * @Route("/page/{page<\d+>}/{limit}-per-page", name="trick_page_with_limit", methods={"GET"})
     */
    public function renderPaginatedTricks(int $page = 1, int $limit = 10, PaginationService $pagination)
    {
        $queryBuilder = $this->trickRepository->createQueryBuilder('item')
            ->orderBy('item.updatedAt', 'DESC');

        $options = $pagination->getRenderOptions('tricks', $queryBuilder, $limit, $page);

        return $this->render('trick/list.html.twig', $options);
    }

    /**
     * @Route("/new", name="trick_new", methods={"GET","POST"})
     * @IsGranted("ROLE_USER", message="You have to be authenticated to create a new trick")
     */
    public function new(Request $request, EntityManagerInterface $em, FileUploaderService $fileUploader): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setCreatedAt(new \DateTime());
            $trick->setUpdatedAt($trick->getCreatedAt());
            $trick->setSlug((new Slugify())->slugify($trick->getName()));

            $pictureForms = $form->get('pictures');

            // Embed pictures forms
            foreach ($pictureForms as $pictureForm) {

                /** @var Picture $picture */
                $picture = $pictureForm->getData();

                /** @var UploadedFile $pictureFile */
                $pictureFile = $pictureForm->get('filename')->getData();

                // this condition is needed because the 'filename' field is not required
                // so the image file must be processed only when a file is uploaded
                if ($pictureFile) {
                    // Upload the new file
                    $pictureFilename = $fileUploader->upload($pictureFile);
                    // updates the 'filename' property to store the image file name
                    // instead of its contents
                    $picture->setFilename($pictureFilename);
                }
            }

            $em->persist($trick);
            $em->flush();

            $this->addFlash('success', 'The trick has been successfully created.');

            return $this->redirectToRoute('trick_show', [
                'group_slug' => $trick->getTrickGroup()->getSlug(),
                'slug' => $trick->getSlug()
            ]);
        }

        return $this->render('trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{group_slug}/{slug}", name="trick_show", methods={"GET", "POST"}, priority=-1)
     */
    public function show(Trick $trick, Request $request, EntityManagerInterface $em): Response
    {
        $currentUser = $this->getUser();
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($currentUser && $form->isSubmitted() && $form->isValid()) {
            $message->setCreatedAt(new \DateTime());
            $message->setTrick($trick);
            $message->setAuthor($currentUser);

            $em->persist($message);
            $em->flush();

            $this->addFlash('success', 'Your message has been successfully added.');

            return $this->redirectToRoute('trick_show', [
                'group_slug' => $trick->getTrickGroup()->getSlug(),
                'slug' => $trick->getSlug()
            ]);
        }

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id<\d+>}/edit", name="trick_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_USER", message="You have to be authenticated to edit a trick")
     */
    public function edit(Request $request, Trick $trick, EntityManagerInterface $em, FileUploaderService $fileUploader): Response
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setUpdatedAt(new \DateTime());
            $trick->setSlug((new Slugify())->slugify($trick->getName()));

            $pictureForms = $form->get('pictures');

            // Embed pictures forms
            foreach ($pictureForms as $pictureForm) {

                /** @var Picture $picture */
                $picture = $pictureForm->getData();

                /** @var UploadedFile $pictureFile */
                $pictureFile = $pictureForm->get('filename')->getData();

                // this condition is needed because the 'filename' field is not required
                // so the image file must be processed only when a file is uploaded
                if ($pictureFile) {
                    // Upload the new file
                    $pictureFilename = $fileUploader->upload($pictureFile);
                    // Remove the old file
                    if (is_file($this->getParameter('pictures_directory') . '/' . $picture->getFilename())) {
                        unlink($this->getParameter('pictures_directory') . '/' . $picture->getFilename());
                    }
                    // updates the 'filename' property to store the image file name
                    // instead of its contents
                    $picture->setFilename($pictureFilename);
                }
            }

            $em->flush();

            $this->addFlash('success', 'The trick has been successfully updated.');

            return $this->redirectToRoute('trick_show', [
                'group_slug' => $trick->getTrickGroup()->getSlug(),
                'slug' => $trick->getSlug()
            ]);
        }

        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="trick_delete", methods={"POST"})
     * @IsGranted("ROLE_USER", message="You have to be authenticated to delete a trick")
     */
    public function delete(Request $request, Trick $trick): Response
    {
        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->request->get('_token'))) {
            $pictures = $trick->getPictures();
            foreach ($pictures as $picture) {
                if (file_exists($this->getParameter('pictures_directory') . '/' . $picture->getFilename())) {
                    unlink($this->getParameter('pictures_directory') . '/' . $picture->getFilename());
                }
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($trick);
            $entityManager->flush();

            $this->addFlash('success', 'The trick has been successfully removed.');
        }

        return $this->redirect(
            $this->generateUrl('homepage') . '#main-content'
        );
    }
}
