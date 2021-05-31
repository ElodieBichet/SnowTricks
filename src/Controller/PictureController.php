<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Entity\Trick;
use App\Form\PictureType;
use App\Service\FileUploaderService;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/picture")
 */
class PictureController extends AbstractController
{
    protected $pictureRepository;

    public function __construct(PictureRepository $pictureRepository)
    {
        $this->pictureRepository = $pictureRepository;
    }

    /**
     * @Route("/all", name="picture_index")
     */
    public function index(): Response
    {
        return $this->render('picture/index.html.twig', [
            'controller_name' => 'PictureController',
        ]);
    }

    /**
     * @Route("/new", name="picture_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, FileUploaderService $fileUploader): Response
    {
        $picture = new Picture();
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $pictureFile */
            $pictureFile = $form->get('filename')->getData();

            // this condition is needed because the 'filename' field is not required
            // so the image file must be processed only when a file is uploaded
            if ($pictureFile) {
                // Upload the file
                $pictureFilename = $fileUploader->upload($pictureFile);
                // updates the 'filename' property to store the image file name
                // instead of its contents
                $picture->setFilename($pictureFilename);
            }

            $em->persist($picture);
            $em->flush();

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
    public function edit(Request $request, EntityManagerInterface $em, Picture $picture, FileUploaderService $fileUploader): Response
    {
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $pictureFile */
            $pictureFile = $form->get('filename')->getData();

            // this condition is needed because the 'filename' field is not required
            // so the image file must be processed only when a file is uploaded
            if ($pictureFile) {
                // Upload the new file
                $pictureFilename = $fileUploader->upload($pictureFile);
                // Remove the old file
                unlink($this->getParameter('pictures_directory') . '/' . $picture->getFilename());
                // updates the 'filename' property to store the image file name
                // instead of its contents
                $picture->setFilename($pictureFilename);
            }

            $em->persist($picture);
            $em->flush();

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

    /**
     * @Route("/{id}", name="picture_delete", methods={"POST"})
     * @IsGranted("ROLE_USER", message="You have to be authenticated to delete a picture")
     */
    public function delete(Request $request, Picture $picture): Response
    {
        if ($this->isCsrfTokenValid('delete' . $picture->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($picture);
            $entityManager->flush();

            $this->addFlash('success', 'The picture has been successfully removed.');
        }

        $trick = $picture->getTrick();

        return $this->redirectToRoute('trick_show', [
            'group_slug' => $trick->getTrickGroup()->getSlug(),
            'slug' => $trick->getSlug()
        ]);
    }
}
