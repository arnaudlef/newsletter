<?php

namespace App\Service;

use App\Entity\Subscriber;
use App\Repository\SubscriberRepository;
use Doctrine\ORM\EntityManagerInterface;

final class SubscriberManager
{
    public function __construct(
        private readonly SubscriberRepository $subscriberRepository,
        private readonly EntityManagerInterface $em,
    ) {}

    public function getOrCreate(string $email, \DateTimeImmutable $birthDate): Subscriber
    {
        $email = mb_strtolower(trim($email));

        $subscriber = $this->subscriberRepository->findOneBy(['email' => $email]);

        if ($subscriber) {
            return $subscriber;
        }

        $subscriber = new Subscriber();
        $subscriber->setEmail($email);
        $subscriber->setBirthDate($birthDate);

        $this->em->persist($subscriber);

        return $subscriber;
    }
}