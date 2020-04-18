<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Form\Type\GalleryType;
use App\Service\FileUploader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/gallery")
 * 
 */
class GalleryController extends AbstractController
{

    public function index()
    {
        $projectDir = $this->getParameter('kernel.project_dir');
        $adminEmail = $this->getParameter('app.admin_email');
    }

    /**
     * @Route("/list", name="galeries", methods={"GET","POST"})
     */
    public function galleryList()
    {
        /** @var GalleryRepository */
        $galleryRepository = $this->getDoctrine()->getRepository(gallery::class);
        $galeries = $galleryRepository->findExceptPast();

        return $this->render(
            'galeries/galeries.html.twig',
            [
                'galeries' => $galeries,
            ]
        );
    }

    /**
     * @Route("/create", name="gallery_create", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function galleryCreate(Request $request, MailerInterface $mailer, FileUploader $fileUploader)
    {
        $user = $this->getUser();

        $gallery = new Gallery;

        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $brochureFile */
            $brochureFile = $form['brochure']->getData();

            if ($brochureFile) {
                $brochureFileName = $fileUploader->upload($brochureFile);
                $product->setBrochureFilename($brochureFileName);
            }

            $gallery = $form->getData();
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

            return $this->redirectToRoute('galeries');
        }

        return $this->render(
            "galeries/gallery_add.html.twig",
            [
                "form" => $form->createView()
            ]
        );
    }

    /**
     * @Route("/{id}/update", name="gallery_update", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function galleryUpdate(Request $request, gallery $gallery)
    {
        $this->denyAccessUnlessGranted('edit', $gallery);

        $form = $this->createForm(galleryType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $brochureFile = $form->get('brochure')->getData();

            if ($brochureFile) {

                $brochureFile = $form['brochure']->getData();
                $destination = $this->getParameter('kernel.project_dir') . '/public/uploads/images';
                $safeFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();
                $brochureFile->move(
                    $destination,
                    $newFilename
                );

                $gallery->setBrochureFilename($newFilename);
            }

            $manager = $this->getDoctrine()->getManager();
            $manager->flush();

            $this->addFlash("success", "L'événement a bien été modifié");

            return $this->redirectToRoute('gallery_view', ['id' => $gallery->getId()]);
        }

        return $this->render(
            "gallerys/edit.html.twig",
            [
                "gallery" => $gallery,
                "form" => $form->createView()
            ]
        );
    }

    /**
     * @Route("/{id}/delete", name="gallery_delete", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function galleryDelete(gallery $gallery)
    {
        $this->denyAccessUnlessGranted('edit', $gallery);

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($gallery);
        $manager->flush();

        $this->addFlash("success", "L'événement a bien été supprimé");

        return $this->redirectToRoute('gallery_list');
    }
}
