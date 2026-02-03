<?php

namespace App\Entity;

use App\Repository\SubscriberRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: SubscriberRepository::class)]
#[ORM\Table(name: 'subscriber')]
#[ORM\UniqueConstraint(name: 'uniq_subscriber_email', columns: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà enregistré.')]
class Subscriber
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private string $email;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $birthDate;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\OneToMany(mappedBy: 'subscriber', targetEntity: Subscription::class, orphanRemoval: true)]
    private Collection $subscriptions;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->subscriptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getBirthDate(): ?\DateTimeImmutable
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeImmutable $birthDate): self
    {
        $this->birthDate = $birthDate;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /** @return Collection<int, Subscription> */
    public function getSubscriptions(): Collection { 
        return $this->subscriptions; 
    }

    public function addSubscription(Subscription $subscription): self
    {
        if (!$this->subscriptions->contains($subscription)) {
            $this->subscriptions->add($subscription);
            $subscription->setSubscriber($this);
        }
        return $this;
    }

    public function removeSubscription(Subscription $subscription): self
    {
        if ($this->subscriptions->removeElement($subscription)) {
            if ($subscription->getSubscriber() === $this) {
                $subscription->setSubscriber(null);
            }
        }
        return $this;
    }
}
