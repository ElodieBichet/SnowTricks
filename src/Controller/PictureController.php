<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Form\PictureType;
use Cocur\Slugify\Slugify;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

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
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
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
                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $pictureFile->guessExtension();

                // Move the file to the directory where pictures are stored
                try {
                    $pictureFile->move(
                        $this->getParameter('pictures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'filename' property to store the image file name
                // instead of its contents
                $picture->setFilename($newFilename);
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
}
