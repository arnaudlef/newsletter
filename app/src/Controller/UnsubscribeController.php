<?php

namespace App\Controller;

use App\Enum\SubscriptionStatus;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UnsubscribeController extends AbstractController
{
    #[Route('/unsubscribe/{id}', name: 'app_unsubscribe', requirements: ['id' => '\d+'])]
    public function __invoke(
        int $id,
        SubscriptionRepository $repository,
        EntityManagerInterface $em
    ): Response {
        $subscription = $repository->findOneBy([ 'id' => $id ]);

        if (!$subscription || $subscription->getStatus() === SubscriptionStatus::REVOKED) {
            return $this->render('unsubscribe/invalid.html.twig', [
                'newsletter' => $subscription->getNewsletter()
            ]);
        }

        $subscription->setStatus(SubscriptionStatus::REVOKED);
        $em->flush();

        return $this->render('unsubscribe/index.html.twig', [
            'newsletter' => $subscription->getNewsletter()
        ]);
    }
}
