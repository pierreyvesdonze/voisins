<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventUserRepository")
 */
class EventUser
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User")
     */
    private $userId;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Event", inversedBy="eventUsers")
     */
    private $eventId;

    public function __construct()
    {
        $this->userId = new ArrayCollection();
        $this->eventId = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|User[]
     */
    public function getUserId(): Collection
    {
        return $this->userId;
    }

    public function addUserId(User $userId): self
    {
        if (!$this->userId->contains($userId)) {
            $this->userId[] = $userId;
        }

        return $this;
    }

    public function removeUserId(User $userId): self
    {
        if ($this->userId->contains($userId)) {
            $this->userId->removeElement($userId);
        }

        return $this;
    }

    /**
     * @return Collection|Event[]
     */
    public function getEventId(): Collection
    {
        return $this->eventId;
    }

    public function addEventId(Event $eventId): self
    {
        if (!$this->eventId->contains($eventId)) {
            $this->eventId[] = $eventId;
        }

        return $this;
    }

    public function removeEventId(Event $eventId): self
    {
        if ($this->eventId->contains($eventId)) {
            $this->eventId->removeElement($eventId);
        }

        return $this;
    }
}
