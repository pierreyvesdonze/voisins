<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Event;
use App\Form\Type\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
                'event' => $event,
            ]
        );
    }

      /**
     * @Route("/create/{id}", name="comment_create", methods={"GET","POST"})
     */
    public function commentCreate(Request $request, Event $event)
    {
        $comment = new Comment;
        $comment->setUser($this->getUser());
        $comment->setEvent($event);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($comment);
            $manager->flush();

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
}
