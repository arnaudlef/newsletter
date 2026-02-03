<?php

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\SubscriptionStatus;

#[ORM\Entity(repositoryClass: SubscriptionRepository::class)]
#[ORM\Table(name: 'subscription')]
class Subscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Subscriber::class, inversedBy: 'subscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Subscriber $subscriber = null;

    #[ORM\Column(length:20, enumType: SubscriptionStatus::class)]
    private SubscriptionStatus $status;

    #[ORM\ManyToOne(targetEntity: Newsletter::class, inversedBy: 'subscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Newsletter $newsletter = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $subscribedAt;

    public function __construct()
    {
        $this->subscribedAt = new \DateTimeImmutable();
        $this->status = SubscriptionStatus::PENDING;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubscriber(): ?Subscriber
    {
        return $this->subscriber;
    }

    public function setSubscriber(?Subscriber $subscriber): self
    {
        $this->subscriber = $subscriber;
        return $this;
    }

    public function getStatus(): SubscriptionStatus 
    {
        return $this->status;
    }

    public function setStatus(SubscriptionStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getNewsletter(): ?Newsletter
    {
        return $this->newsletter;
    }

    public function setNewsletter(?Newsletter $newsletter): self
    {
        $this->newsletter = $newsletter;
        return $this;
    }

    public function getSubscribedAt(): ?\DateTimeImmutable
    {
        return $this->subscribedAt;
    }
}
