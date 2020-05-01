<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Entity\Photo;
use App\Form\Type\GalleryType;
use App\Repository\GalleryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;


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
        return $this->render(
            "galeries/index.html.twig",
            [
                'gallery' => $galleryRepository->findAll()
            ]
        );
    }

    /**
     * @Route("/{id}", name="gallery_show", methods={"GET"})
     */
    public function show(Gallery $gallery): Response
    {

        return $this->render('galeries/show.html.twig', [
            'gallery' => $gallery,
        ]);
    }

    /**
     * @Route("/create/new", name="gallery_create", methods={"GET","POST"})
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

            foreach ($photos as $photo) {
                $fichier = md5(uniqid()) . '.' . $photo->guessExtension();

                $photo->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );

                $img = new Photo();
                $img->setTitle($fichier);
                $gallery->addPhoto($img);
            }

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($gallery);
            $manager->flush();

            $this->addFlash("success", "La galerie a bien été créé !");

            $message = (new TemplatedEmail())
                ->from('pyd3.14@gmail.com')
                ->to(
                    'pyd3.14@gmail.com',
                    'blubelly@hotmail.fr',
                    'mo.villemard@laposte.net',
                    'renaud.vaudeville@gmail.com',
                    'fredericcesar@hotmail.fr',
                    'floriane.dechamp@gmail.com'
                )
                ->subject('Nouvel événement de "voisins"')
                ->htmlTemplate('galeries/notification.html.twig')
                ->context([
                    'user'  => $user,
                    'gallery' => $gallery
                ]);

            $mailer->send($message);

            return $this->redirectToRoute('gallery_index');
        }

        return $this->render(
            "galeries/new.html.twig",
            [
                "form"    => $form->createView(),
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

        if ($form->isSubmitted() && $form->isValid()) {

            $photos = $form->get('photos')->getData();

            foreach ($photos as $photo) {
                $fichier = md5(uniqid()) . '.' . $photo->guessExtension();

                $photo->move(
                    $this->getParameter('image_directory'),
                    $fichier
                );

                $img = new Photo();
                $img->setTitle($fichier);
                $gallery->addPhoto($img);
            }

            $manager->persist($gallery);
            $manager->flush();

            $this->addFlash("success", "La galerie a bien été mise à jour !");

            return $this->redirectToRoute('gallery_update');            
        }

        return $this->render('galeries/edit.html.twig', [
            'gallery' => $gallery,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="gallery_delete", methods={"DELETE"})
     */
    public function deleteGallery(Gallery $gallery, Request $request)
    {
        if ($this->isCsrfTokenValid('delete' . $gallery->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($gallery);
            $em->flush();
        }
        return $this->redirectToRoute('galeries');
    }

    /**
     * @Route("/delete/photo/{id})", name="delete_photo", methods={"DELETE"})
     */
    public function deletePhoto(Photo $photo, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        // On vérifie si le token est valide
        if ($this->isCsrfTokenValid('delete' . $photo->getId(), $data['_token'])) {
            // On récupère le nom de l'image
            $name = $photo->getTitle();
            // On supprime le fichier
            unlink($this->getParameter('images_directory') . '/' . $name);

            $em = $this->getDoctrine()->getManager();
            $em->remove($photo);
            $em->flush();

            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }
}
