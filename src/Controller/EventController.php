<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Event;
use App\Entity\Participate;
use App\Entity\ShoppingList;
use App\Entity\User;
use App\Form\Type\EventType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
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
        //$events = $eventRepository->findAll();

        $events = $eventRepository->findExceptPast();

        return $this->render(
            'events/list.html.twig',
            [
                'events' => $events,
            ]
        );
    }

    /**
     * @Route("/archive-list", name="archive_list", methods={"GET","POST"})
     */
    public function archiveList()
    {
        /** @var eventRepository */
        $eventRepository = $this->getDoctrine()->getRepository(Event::class);
        //$events = $eventRepository->findAll();

        $events = $eventRepository->findPastEvents();

        return $this->render(
            'events/archives.html.twig',
            [
                'events' => $events,
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

        /** @var CommentRepository */
        $commentRepository = $this->getDoctrine()->getRepository(Comment::class);
        $comments = $commentRepository->findBy(["event" => $event->getId()]);

        return $this->render('events/view.html.twig', [
            'event'         => $event,
            'shoppingLists' => $shoppingLists,
            'participates'  => $participates,
            'comments'      => $comments
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
    public function eventCreate(Request $request, MailerInterface $mailer)
    {
        $user = $this->getUser();

        $event = new Event;
        $event->setUser($user);

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = $form->getData();
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($event);
            $manager->flush();

            $this->addFlash("success", "L'événement a bien été ajouté !");

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
                ->htmlTemplate('emails/notification.html.twig')
                ->context([
                    'user'  => $user,
                    'event' => $event
                ]);

            $mailer->send($message);

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
    public function eventUpdate(Request $request, Event $event)
    {
        $this->denyAccessUnlessGranted('edit', $event);

        $form = $this->createForm(eventType::class, $event);
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

                $event->setBrochureFilename($newFilename);
            }

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
        $this->denyAccessUnlessGranted('edit', $event);

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($event);
        $manager->flush();

        $this->addFlash("success", "L'événement a bien été supprimé");

        return $this->redirectToRoute('event_list');
    }
}
