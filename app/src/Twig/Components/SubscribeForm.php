<?php

namespace App\Twig\Components;

use App\Form\Model\SubscribeData;
use App\Form\SubscribeType;
use App\Entity\Subscriber;
use App\Entity\Newsletter;
use App\Factory\SubscriberFactory;
use App\Factory\SubscriptionFactory;
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

        $subscriber = SubscriberFactory::findOrCreate(['email' => mb_strtolower(trim($data->email)), 'birthDate' => $data->birthDate]);

        $ageOk = $data->birthDate !== null && $this->checker->isAgeOk($data->birthDate);
        $status = $ageOk
            ? \App\Enum\SubscriptionStatus::ACCEPTED
            : \App\Enum\SubscriptionStatus::REFUSED;

        $this->subscribe($subscriber, $data->newsletters, $status);

        try {
            $this->em->flush();
        } catch (UniqueConstraintViolationException) {
            $subscriber = $this->subscriberRepository->findOneBy(['email' => mb_strtolower(trim($data->email))]);
        }

        if (!$ageOk) {
            $this->mailerService->sendRefused($data->email);
            $this->isSubmitted = true;
            $this->addFlash('error', 'Désolé, il faut avoir plus de 16 ans.');
            return;
        }

        $this->mailerService->sendAccepted($subscriber->getEmail(), $data->newsletters);

        $this->isSubmitted = true;

        $this->addFlash('success', 'Vous êtes inscrit à la newsletter !');
    }

    /**
     * @param iterable<\App\Entity\Newsletter> $newsletters
     */
    public function subscribe(Subscriber $subscriber, iterable $newsletters, SubscriptionStatus $status): void
    {
        foreach($newsletters as $newsletter) {
            $subscription = SubscriptionFactory::findBy([
                'subscriber' => $subscriber,
                'newsletter' => $newsletter
            ]);

            if ($subscription) {
                $subscription->setStatus($status);
                continue;
            }

            SubscriptionFactory::createOne([
                'subscriber' => $subscriber,
                'newsletter' => $newsletter,
                'status' => $status
            ]);
        }
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
