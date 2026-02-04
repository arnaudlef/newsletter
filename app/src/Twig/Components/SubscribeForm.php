<?php

namespace App\Twig\Components;

use App\Form\Model\SubscribeData;
use App\Form\SubscribeType;
use App\Service\AgeChecker;
use App\Service\SubscriberManager;
use App\Service\SubscriptionManager;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;
use Symfony\UX\LiveComponent\ValidatableComponentTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use App\Enum\SubscriptionStatus;
use Symfony\Component\Mailer\MailerInterface;

#[AsLiveComponent]
final class SubscribeForm extends AbstractController
{
    use DefaultActionTrait;
    use ValidatableComponentTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public ?SubscribeData $initialFormData = null;

    #[LiveProp]
    public ?string $ageMessage = null;

    #[LiveProp]
    public ?bool $isSubmitted = false;
    
    public function __construct(
        private readonly AgeChecker $checker,
        private readonly EntityManagerInterface $em,
        private readonly SubscriberManager $subscriberManager,
        private readonly SubscriptionManager $subscriptionManager,
        private readonly MailerService $mailerService,
    ) {
        $this->data = new SubscribeData();
    }

    protected function instantiateForm(): FormInterface
    {
        $this->isSubmitted = false;
        return $this->createForm(SubscribeType::class, $this->data);
    }

    public function getIsAgeEligible(): ?bool
    {
        $data = $this->getForm()->getData();

        if (!$data->birthDate) {
            return null;
        }
        return $this->checker->isAgeOk($data->birthDate);
    }

    #[LiveAction]
    public function save(): void
    {
        $this->submitForm();

        /** @var \App\Form\Model\SubscribeData $data */
        $data = $this->form->getData();
        
        $this->ageMessage = null;
        
        $subscriber = $this->subscriberManager->getOrCreate($data->email, $data->birthDate);

        if (!$this->checker->isAgeOk($data->birthDate)) {
            $this->em->flush();

            $this->mailerService->sendRefused($subscriber->getEmail());

            $this->ageMessage = sprintf('Désolé, il faut avoir au moins %d ans.', $this->checker->minAge());
            return;
        }

        $this->subscriptionManager->applySubscriptions(
            $subscriber,
            $data->newsletters,
            SubscriptionStatus::ACCEPTED
        );

        try {
            $this->em->flush();
        } catch (UniqueConstraintViolationException) {
            $subscriber = $this->subscriberRepository->findOneBy(['email' => mb_strtolower(trim($data->email))]);
        }

        $this->mailerService->sendAccepted($subscriber->getEmail(), $data->newsletters);

        $this->ageMessage = null;
        $this->isSubmitted = true;

        $this->addFlash('success', 'Vous êtes inscrit à la newsletter !');
    }

    public function getCanSubmit(): bool
    {
        /** @var \App\Form\Model\SubscribeData $data */
        $data = $this->getForm()->getData();

        if (!$data->email || !filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        if (!$data->birthDate) {
            return false;
        }

        if (!$data->newsletters || \count($data->newsletters) < 1) {
            return false;
        }

        return true;
    }
}
