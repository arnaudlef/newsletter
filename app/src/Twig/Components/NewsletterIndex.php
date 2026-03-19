<?php

namespace App\Twig\Components;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use App\Repository\NewsletterRepository;

#[AsLiveComponent]
final class NewsletterIndex extends AbstractController
{
    use DefaultActionTrait;

    public function __construct(
        private readonly NewsletterRepository $newsletterRepository,
    ) {}

    /**
     * @return array<\App\Entity\Newsletter>
     */
    public function getNewsletters(): array
    {
        return $this->newsletterRepository->findAll();
    }

    #[LiveListener('newsletter:updated')]
    public function refresh(): void
    {}
}
