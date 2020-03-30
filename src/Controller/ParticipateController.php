<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Participate;
use App\Form\Type\ParticipateType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * @Route("/participate")
 */
class ParticipateController extends AbstractController
{
    public function index()
    {
        $projectDir = $this->getParameter('kernel.project_dir');
        $adminEmail = $this->getParameter('app.admin_email');
    }

    /**
     * @Route("/list/{id}", name="participate_list")
     */
    public function participate(Participate $participate)
    {
        return $this->render('participate/view.html.twig', [
            'participate' => $participate
        ]);
    }

    /**
     * @Route("/request/{id}", name="participate_request", methods={"GET","POST"})
     */
    public function participateRequest(Request $request, Event $event): Response
    {
        $participate = new Participate();
        $participate->setEvent($event);
        $participate->setUser($this->getUser());

        $form = $this->createForm(ParticipateType::class, $participate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $response = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($participate);
            $entityManager->flush();

            $this->addFlash('success', 'Enregistré.');

            return $this->redirectToRoute('event_view', ['id' => $participate->getEvent()->getId()]);
        }

        return $this->render('participate/create.html.twig', [
            'participate' => $participate,
            'event'        => $event,
            'form'         => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/update", name="participate_update", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function participateUpdate(Request $request, Participate $participate)
    {
        $this->denyAccessUnlessGranted('edit', $participate);

        $form = $this->createForm(ParticipateType::class, $participate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager = $this->getDoctrine()->getManager();
            $manager->flush();

            $this->addFlash("success", "Modifié !!");

            return $this->redirectToRoute('participate_list', ['id' => $participate->getId()]);
        }

        return $this->render(
            "participate/update.html.twig",
            [
                "form" => $form->createView()
            ]
        );
    }

    /**
     * @Route("/{id}/delete", name="participate_delete", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function participateDelete(Participate $participate)
    {
        $this->denyAccessUnlessGranted('edit', $participate);

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($participate);
        $manager->flush();

        $this->addFlash("success", "Supprimé");

        return $this->redirectToRoute('event_view', ['id' => $participate->getEvent()->getId()]);
    }
}
