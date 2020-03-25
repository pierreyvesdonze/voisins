<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Participate;
use App\Entity\ShoppingList;
use App\Form\Type\EventType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/event")
 * 
 */
class EventController extends AbstractController
{

    public function index()
    {
        $projectDir = $this->getParameter('kernel.project_dir');
        $adminEmail = $this->getParameter('app.admin_email');
    }

    /**
     * @Route("/list", name="event_list", methods={"GET","POST"})
     */
    public function eventList()
    {
        /** @var eventRepository */
        $eventRepository = $this->getDoctrine()->getRepository(Event::class);
        $events = $eventRepository->findAll();

        return $this->render(
            'events/list.html.twig',
            [
                'events'        => $events,
            ]
        );
    }

    /**
     * @Route("/view/{id}", name="event_view", methods={"GET","POST"})
     */
    public function eventView(Event $event)
    {
        /** @var ShoppingListRepository */
        $shoppingListRepository = $this->getDoctrine()->getRepository(ShoppingList::class);
        $shoppingLists = $shoppingListRepository->findBy(["event" => $event->getId()]);

        /** @var ParticipateRepository */
        $participateRepository = $this->getDoctrine()->getRepository(Participate::class);
        $participates = $participateRepository->findBy(["event" => $event->getId()]);

        //dd($participates);

        return $this->render('events/view.html.twig', [
            'event'         => $event,
            'shoppingLists' => $shoppingLists,
            'participates'   => $participates
        ]);
    }

    /**
     * @Route("/participate-view/{id}", name="participate_view", methods={"GET","POST"})
     */
    public function participateView(Participate $participate)
    {

        return $this->render('participate/view.html.twig', [
            'participate'   => $participate
        ]);
    }

    /**
     * @Route("/create", name="event_create", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function eventCreate(Request $request)
    {
        $event = new Event;
        $event->setUser($this->getUser());

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = $form->getData();
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($event);
            $manager->flush();

            $this->addFlash("success", "L'événement a bien été ajouté !");

            return $this->redirectToRoute('event_list');
        }

        return $this->render(
            "events/event_add.html.twig",
            [
                "formView" => $form->createView()
            ]
        );
    }

    /**
     * @Route("/{id}/update", name="event_update", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function eventUpdate(Request $request, event $event)
    {
        $this->denyAccessUnlessGranted('edit', $event);

        $form = $this->createForm(eventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager = $this->getDoctrine()->getManager();
            $manager->flush();

            $this->addFlash("success", "L'événement a bien été modifié");

            return $this->redirectToRoute('event_list', ['id' => $event->getId()]);
        }

        return $this->render(
            "events/edit.html.twig",
            [
                "form" => $form->createView()
            ]
        );
    }

    /**
     * @Route("/{id}/delete", name="event_delete", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function eventDelete(Event $event)
    {

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($event);
        $manager->flush();

        $this->addFlash("success", "L'événement a bien été supprimé");

        return $this->redirectToRoute('event_list');
    }
}
