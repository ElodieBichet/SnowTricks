<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Form\PictureType;
use App\Event\FileUpdateEvent;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/picture")
 */
class PictureController extends AbstractController
{
    protected $pictureRepository;
    protected $dispatcher;
    protected $entityManager;

    public function __construct(
        PictureRepository $pictureRepository,
        EventDispatcherInterface $dispatcher,
        EntityManagerInterface $entityManager
    ) {
        $this->pictureRepository = $pictureRepository;
        $this->dispatcher = $dispatcher;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/admin", name="picture_admin")
     * @IsGranted("ROLE_ADMIN", message="You have to be authenticated as an admin to see this page")
     */
    public function index(): Response
    {
        return $this->render('admin/picture/index.html.twig', [
            'pictures' => $this->pictureRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="picture_new", methods={"GET","POST"})
     * @IsGranted("ROLE_USER", message="You have to be authenticated to create a picture")
     */
    public function new(Request $request): Response
    {
        $picture = new Picture();
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->processForm($form, $picture);

            $this->addFlash('success', 'The picture has been successfully added.');

            return $this->redirectToRoute('trick_show', [
                'group_slug' => $picture->getTrick()->getTrickGroup()->getSlug(),
                'slug' => $picture->getTrick()->getSlug()
            ]);
        }

        return $this->render('picture/new.html.twig', [
            'picture' => $picture,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id<\d+>}/edit", name="picture_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_USER", message="You have to be authenticated to edit a picture")
     */
    public function edit(Request $request, Picture $picture): Response
    {
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->processForm($form, $picture);

            $this->addFlash('success', 'The picture has been successfully updated.');

            return $this->redirectToRoute('trick_show', [
                'group_slug' => $picture->getTrick()->getTrickGroup()->getSlug(),
                'slug' => $picture->getTrick()->getSlug()
            ]);
        }

        return $this->render('picture/edit.html.twig', [
            'picture' => $picture,
            'form' => $form->createView()
        ]);
    }

    protected function processForm($form, Picture $picture)
    {
        /** @var UploadedFile $pictureFile */
        $pictureFile = $form->get('filename')->getData();

        // this condition is needed because the 'filename' field is not required
        // so the image file must be processed only when a file is uploaded
        if ($pictureFile) {
            $event = ($picture->getFilename()) ? 'file.update' : 'file.new';
            $this->dispatcher->dispatch(new FileUpdateEvent($picture, $pictureFile), $event);
        }

        $this->entityManager->persist($picture);
        $this->entityManager->flush();
    }
}
