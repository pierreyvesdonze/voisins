<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Entity\Photo;
use App\Form\Type\GalleryType;
use App\Repository\GalleryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Intervention\Image\ImageManager;

/**
 * @Route("/gallery")
 */
class GalleryController extends AbstractController
{

    public function index()
    {
        $projectDir = $this->getParameter('kernel.project_dir');
        $adminEmail = $this->getParameter('app.admin_email');
    }

    /**
     * @Route("/index", name="gallery_index", methods={"GET"})
     */
    public function galeriesIndex(GalleryRepository $galleryRepository): Response
    {
        $gallery = $galleryRepository->findAll();

        return $this->render(
            "galeries/index.html.twig",
            [
                'gallery' => $gallery,
            ]
        );
    }

    /**
     * @Route("/{id}", name="gallery_show", methods={"GET"})
     */
    public function show(Gallery $gallery): Response
    {
        $galleryTitle = $gallery->getTitle();
        $split = explode('.', $galleryTitle);
        $galleryUserTitle = $split[0];

        //dump($galleryTitle);

        return $this->render('galeries/show.html.twig', [
            'gallery' => $gallery,
            'galleryTitle' => $galleryTitle,
            'galleryUserTitle' => $galleryUserTitle
        ]);
    }

    /**
     * @Route("/create/new", name="gallery_create", methods={"GET","POST"}, options = { "expose" = true })
     */
    public function galleryCreate(Request $request, MailerInterface $mailer): Response
    {
        $user = $this->getUser();

        $gallery = new Gallery();
        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Get submitted photos
            $photos = $form->get('photos')->getData();

            // Create a new folder
            $filesystem = new Filesystem();

            //make a new directory into current
            $galleryDirectory = $this->getParameter('images_directory');
            $galleryNewTitle = $gallery->getTitle() . '.' . md5(uniqid());

            $gallery->setTitle($galleryNewTitle);
            $newGalleryPath = $galleryDirectory
                . "/"
                . $galleryNewTitle;

            $filesystem->mkdir($newGalleryPath);
            $gallery->setPath($newGalleryPath);
            $gallery->setUser($user);

            // Move new photos into new directory
            foreach ($photos as $photo) {

                // Resize photos
                $ImgManager = new ImageManager(array('driver' => 'imagick'));
                $resizedPhoto = $ImgManager->make($photo)->resize(800, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
            

                $photoName = md5(uniqid()) . '.' . $resizedPhoto . '.jpeg';

                $resizedPhoto->save($newGalleryPath . '/' . $photoName);
   /*              $resizedPhoto->move($newGalleryPath); */

                $img = new Photo();
                $img->setTitle($photoName);
                $gallery->addPhoto($img);
            }

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($gallery);
            $manager->flush();

            $this->addFlash("success", "La galerie a bien été créée !");

            return $this->redirectToRoute('gallery_show', ['id' => $gallery->getId()]);
        }

        return $this->render(
            "galeries/new.html.twig",
            [
                "form" => $form->createView(),
                "gallery" => $gallery
            ]
        );
    }

    /**
     * @Route("/{id}/update", name="gallery_update", methods={"GET","POST"})
     */
    public function galleryUpdate(Request $request, Gallery $gallery): Response
    {
        $manager = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);

        $galleryTitle = $gallery->getTitle();
        $split = explode('.', $galleryTitle);
        $galleryUserTitle = $split[0];

        if ($form->isSubmitted() && $form->isValid()) {

            $photos = $form->get('photos')->getData();

            foreach ($photos as $photo) {

                $fichier = md5(uniqid()) . '.' . $photo->guessExtension();

                $photo->move(
                    $gallery->getPath(),
                    $fichier
                );

                $img = new Photo();
                $img->setTitle($fichier);
                $gallery->addPhoto($img);
            }

            $manager->flush();

            $this->addFlash("success", "La galerie a bien été mise à jour !");

            return $this->redirectToRoute('gallery_show', ['id' => $gallery->getId()]);
        }

        return $this->render('galeries/edit.html.twig', [
            'gallery' => $gallery,
            'form' => $form->createView(),
            'galleryTitle' => $galleryTitle,
            'galleryUserTitle' => $galleryUserTitle
        ]);
    }

    /**
     * @Route("/{id}/delete", name="gallery_delete", methods="DELETE")
     */
    public function deleteGallery(Gallery $gallery, Request $request)
    {
        if ($this->isCsrfTokenValid('delete' . $gallery->getId(), $request->request->get('_token'))) {
            $filesystem = new Filesystem;
            $imagesDirectory = $this->getParameter('images_directory');
            $galleryDirectory = $gallery->getTitle();

            $em = $this->getDoctrine()->getManager();
            $em->remove($gallery);
            $em->flush();
            $filesystem->remove($imagesDirectory . '/' . $galleryDirectory);
        }

        return $this->redirectToRoute('gallery_index');
    }

    /**
     * @Route("/delete/photo/{id}", name="delete_photo", methods={"DELETE"})
     */
    public function deletePhoto(Photo $photo, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $filesystem = new Filesystem;
        $imagesDirectory = $this->getParameter('images_directory');
        $galleryDirectory = $photo->getGallery()->getTitle();

        // On vérifie si le token est valide
        if ($this->isCsrfTokenValid('delete' . $photo->getId(), $data['_token'])) {
            // On récupère le nom de l'image
            $name = $photo->getTitle();
            // On supprime le fichier

            $filesystem->remove($imagesDirectory . '/' . $galleryDirectory . '/' . $name);

            $em = $this->getDoctrine()->getManager();
            $em->remove($photo);
            $em->flush();

            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }
}
