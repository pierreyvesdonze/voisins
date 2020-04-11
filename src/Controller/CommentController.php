<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Event;
use App\Entity\User;
use App\Form\Type\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

/**
 * @Route("/comment")
 * 
 */
class CommentController extends AbstractController
{

    public function index()
    {
        $projectDir = $this->getParameter('kernel.project_dir');
        $adminEmail = $this->getParameter('app.admin_email');
    }

    /**
     * @Route("/list/{id}", name="comment_list", methods={"GET","POST"})
     */
    public function commentList(Event $event)
    {
        /** @var CommentRepository */
        $commentRepository = $this->getDoctrine()->getRepository(Comment::class);
        $comments = $commentRepository->findBy(["event" => $event->getId()]);

        return $this->render(
            'events/list.html.twig',
            [
                'comments' => $comments,
                'event' => $event,
            ]
        );
    }

    /**
     * @Route("/create/{id}", name="comment_create", methods={"GET","POST"})
     */
    public function commentCreate(Request $request, Event $event, MailerInterface $mailer)
    {
        $comment = new Comment;
        $user = $this->getUser();
        $comment->setUser($user);
        $comment->setEvent($event);

        $eventOwnerRepository = $this->getDoctrine()->getRepository(User::class);
        $eventOwner = $eventOwnerRepository->findOneBy(['id' => $event->getUser()]);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($comment);
            $manager->flush();

            $message = (new TemplatedEmail())
                ->from('pyd3.14@gmail.com')
                ->to(
                    $eventOwner->getEmail(),
                    $user->getEmail(),
                    'pyd3.14@gmail.com'
                )
                ->subject('Nouvel événement de "voisins"')
                ->htmlTemplate('emails/comment.notification.html.twig')
                ->context([
                    'user'  => $user,
                    'event' => $event
                ]);

            $mailer->send($message);

            $this->addFlash("success", "Merci pour ton commentaire !");
            return $this->redirectToRoute('event_view', ['id' => $event->getId()]);
        }

        return $this->render(
            'comments/create.html.twig',
            [
                "event" => $event,
                "form"  => $form->createView()
            ]
        );
    }

    /**
     * @Route("/{id}/update", name="comment_update", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function commentUpdate(Request $request, Comment $comment)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager = $this->getDoctrine()->getManager();
            $manager->flush();

            $this->addFlash("success", "Modifié !!");

            return $this->redirectToRoute('event_view', ['id' => $comment->getEvent()->getId()]);
        }

        return $this->render(
            "comments/update.html.twig",
            [
                "comment" => $comment,
                "form" => $form->createView()
            ]
        );
    }
    /**
     * @Route("/{id}/delete", name="comment_delete", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function commentDelete(Comment $comment)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($comment);
        $manager->flush();

        return $this->redirectToRoute('event_view', ['id' => $comment->getEvent()->getId()]);
    }
}
