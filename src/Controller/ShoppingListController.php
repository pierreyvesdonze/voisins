<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\ShoppingList;
use App\Entity\ShoppingListIngredient;
use App\Form\Type\ShoppingListType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function groceriesRequest(Request $request, Event $event): Response
    {

        $shoppingList = new ShoppingList();
        $shoppingList->setEvent($event);
        $shoppingList->setUser($this->getUser());

        $form = $this->createForm(ShoppingListType::class, $shoppingList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($shoppingList);
            $entityManager->flush();

            $this->addFlash('success', 'Enregistré.');

            return $this->redirectToRoute('event_list');
        }

        return $this->render('groceries/create.html.twig', [
            'shoppingList' => $shoppingList,
            'event'        => $event,
            'form'         => $form->createView(),
        ]);
    }
}
