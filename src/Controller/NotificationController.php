<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Email;

class NotificationController extends AbstractController
{
    
    /**
     * @Route("/email", name="email")
     */
    public function sendEmail(MailerInterface $mailer)
    {
        $email = (new Email())
            ->from('admin@voisins.com')
            ->to('pyd3.14@gmail.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');


        $mailer->send($email); 

        return $this->redirectToRoute('event_list');
    }
}