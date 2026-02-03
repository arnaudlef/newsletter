<?php

namespace App\Service;

use App\Entity\Newsletter;
use App\Entity\Subscriber;
use App\Entity\Subscription;
use App\Enum\SubscriptionStatus;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;

final class SubscriptionManager
{
    public function __construct(
        private readonly SubscriptionRepository $subscriptionRepository,
        private readonly EntityManagerInterface $em,
    ) {}

    /**
     * @param iterable<Newsletter> $selectedNewsletters
     */
    public function applySubscriptions(
        Subscriber $subscriber,
        iterable $selectedNewsletters,
        SubscriptionStatus $status
    ): void {
        foreach ($selectedNewsletters as $newsletter) {
            $existing = $this->subscriptionRepository->findOneBy([
                'subscriber' => $subscriber,
                'newsletter' => $newsletter,
            ]);

            if ($existing) {
                $existing->setStatus($status);
                continue;
            }

            $sub = new Subscription();
            $sub->setSubscriber($subscriber);
            $sub->setNewsletter($newsletter);
            $sub->setStatus($status);

            $this->em->persist($sub);
        }
    }
}