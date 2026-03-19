<?php

namespace App\Twig\Components;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use App\Entity\Newsletter;
use App\Enum\NewsletterStatus;
use App\Repository\NewsletterRepository;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentToolsTrait;

#[AsLiveComponent]
final class NewsletterInput
{
    use DefaultActionTrait;
    use ComponentToolsTrait;

    #[LiveProp]
    public Newsletter $newsletter;

    public function __construct(
        private readonly NewsletterRepository $newsletterRepository,
    ) {}

    public function getStatus(): NewsletterStatus
    {
        return $this->newsletter->getStatus();
    }

    #[LiveAction]
    public function save(): void 
    {
        if($this->newsletter->getStatus() != NewsletterStatus::PUBLISHED) {
            $this->newsletter->setStatus(NewsletterStatus::PUBLISHED);
        }
        else {
            $this->newsletter->setStatus(NewsletterStatus::UNPUBLISHED);
        }
        $this->newsletterRepository->flush();
    }

    #[LiveAction]
    public function delete(): void 
    {
        $this->newsletterRepository->remove($this->newsletter);
        $this->emitUp('newsletter:updated');
    }
}
