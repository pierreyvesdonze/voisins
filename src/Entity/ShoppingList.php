<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ShoppingListRepository")
 */
class ShoppingList
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $title;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="shoppingList")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ShoppingListIngredient", mappedBy="shoppingList", cascade={"remove"} ) 
     */
    private $shoppingListIngredients;


    public function __construct()
    {
        $this->ingredients = new ArrayCollection();
        $this->shoppingListIngredients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|ShoppingListIngredient[]
     */
    public function getShoppingListIngredients(): Collection
    {
        return $this->shoppingListIngredients;
    }

    public function addShoppingListIngredient(ShoppingListIngredient $shoppingListIngredient): self
    {
        if (!$this->shoppingListIngredients->contains($shoppingListIngredient)) {
            $this->shoppingListIngredients[] = $shoppingListIngredient;
            $shoppingListIngredient->setShoppingList($this);
        }

        return $this;
    }

    public function removeShoppingListIngredient(ShoppingListIngredient $shoppingListIngredient): self
    {
        if ($this->shoppingListIngredients->contains($shoppingListIngredient)) {
            $this->shoppingListIngredients->removeElement($shoppingListIngredient);
            // set the owning side to null (unless already changed)
            if ($shoppingListIngredient->getShoppingList() === $this) {
                $shoppingListIngredient->setShoppingList(null);
            }
        }
        return $this;
    }  
}
