<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ShoppingListIngredientRepository")
 */
class ShoppingListIngredient
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ingredient",
     *  inversedBy="shoppingListIngredients",
     *  fetch="EAGER",
     * cascade={"persist"})
     */
    private $ingredient;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ShoppingList",
     *  inversedBy="shoppingListIngredients",
     *  fetch="EAGER",
     * cascade={"persist"})
     * )
     */
    private $shoppingList;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIngredient(): ?Ingredient
    {
        return $this->ingredient;
    }

    public function setIngredient(?Ingredient $ingredient): self
    {
        $this->ingredient = $ingredient;

        return $this;
    }

    public function getShoppingList(): ?ShoppingList
    {
        return $this->shoppingList;
    }

    public function setShoppingList(?ShoppingList $shoppingList): self
    {
        $this->shoppingList = $shoppingList;

        return $this;
    }
}
