<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Event;
use App\Entity\ShoppingList;
use App\Form\Type\ShoppingListType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

/**
 * @Route("/groceries")
 */
class ShoppingListController extends AbstractController
{
    public function index()
    {
        $projectDir = $this->getParameter('kernel.project_dir');
        $adminEmail = $this->getParameter('app.admin_email');
    }

    /**
     * @Route("/list/{id}", name="shopping_list")
     */
    public function shoppingList(ShoppingList $shoppingList)
    {
        return $this->render('groceries/view.html.twig', [
            'shoppingList' => $shoppingList
        ]);
    }

    /**
     * @Route("/request/{id}", name="groceries_request", methods={"GET","POST"})
     */
    public function groceriesRequest(Request $request, Event $event, MailerInterface $mailer): Response
    {
        $shoppingList = new ShoppingList();
        $shoppingList->setEvent($event);
        $user = $this->getUser();
        $shoppingList->setUser($user);

        $article = new Article();
        $article->setShoppingList($shoppingList);
        $shoppingList->getArticles()->add($article);

        $form = $this->createForm(ShoppingListType::class, $shoppingList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($shoppingList);
            $entityManager->persist($article);

            $entityManager->flush();

            $this->addFlash('success', 'Enregistré.');

            $message = (new TemplatedEmail())
                ->from('pyd3.14@gmail.com')
                ->to(
                    'pyd3.14@gmail.com',
                   
                )
                ->subject('Nouvel événement de "voisins"')
                ->htmlTemplate('emails/shoplist.notification.html.twig')
                ->context([
                    'user'  => $user,
                    'event' => $event
                ]);

            $mailer->send($message);

            return $this->redirectToRoute('event_view', ['id' => $event->getId()]);
        }

        return $this->render('groceries/create.html.twig', [
            'shoppingList' => $shoppingList,
            'event'        => $event,
            'form'         => $form->createView(),
        ]);
    }

     /**
     * @Route("/{id}/update", name="shopping_list_update", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function shoppingListUpdate(Request $request, ShoppingList $shoppingList)
    {
        $this->denyAccessUnlessGranted('edit', $shoppingList);

        $manager = $this->getDoctrine()->getManager();

        if (null === $shoppingList ){
            throw $this->createNotFoundException('No task found for id '.$shoppingList->getId);
        }

        $originalArticles = new ArrayCollection();

        // Create an ArrayCollection of the current Article objects in the database
        foreach ($shoppingList->getArticles() as $article) {
            $originalArticles->add($article);
        }
    
        $form = $this->createForm(ShoppingListType::class, $shoppingList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            foreach ($originalArticles as $article) {
                if (false === $shoppingList->getArticles()->contains($article)) {
               
                    // if it was a many-to-one relationship, remove the relationship like this
                    $article->setShoppingList(null);
    
                    $manager->persist($article);
    
                    // if you wanted to delete the Tag entirely, you can also do that
                    // $entityManager->remove($tag);
                }
            }
    
            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('shopping_list', ['id' => $shoppingList->getId()]);
        }

        return $this->render(
            "groceries/update.html.twig",
            [
                "form" => $form->createView()
            ]
        );
    }

    /**
     * @Route("/{id}/delete", name="shopping_list_delete", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function shoppingListDelete(ShoppingList $shoppingList)
    {
        $this->denyAccessUnlessGranted('edit', $shoppingList);

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($shoppingList);
        $manager->flush();

        $this->addFlash("success", "La liste de courses a bien été supprimé");

        return $this->redirectToRoute('event_view', ['id' => $shoppingList->getEvent()->getId()]);
    }
}
