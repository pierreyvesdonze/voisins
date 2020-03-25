<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\ShoppingList;
use App\Form\Type\ShoppingListType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

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

     /**
     * @Route("/{id}/update", name="shopping_list_update", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function shoppingListUpdate(Request $request, ShoppingList $shoppingList)
    {
        $this->denyAccessUnlessGranted('edit', $shoppingList);

        $form = $this->createForm(ShoppingListType::class, $shoppingList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager = $this->getDoctrine()->getManager();
            $manager->flush();

            $this->addFlash("success", "La liste de courses a bien été modifié");

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

        return $this->redirectToRoute('shopping_list');
    }
}
