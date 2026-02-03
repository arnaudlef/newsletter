<?php

namespace App\Twig\Components;

use App\Form\Model\SubscribeData;
use App\Form\SubscribeType;
use App\Service\AgeChecker;
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
    public bool $submitted = false;
    
    public function __construct(
        private readonly AgeChecker $checker,
        private readonly EntityManagerInterface $em,
    ) {
        $this->data = new SubscribeData();
    }

    protected function instantiateForm(): FormInterface
    {
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
        $this->submitted = true;

        $this->submitForm();

        /** @var SubscribeData $data */
        $data = $this->form->getData();

        var_dump($data);

        if ($data->birthDate && !$this->checker->isAgeOk($data->birthDate)) {
            $this->ageMessage = sprintf(
                'Désolé, il faut avoir au moins %d ans pour s’abonner.',
                $this->checker->minAge()
            );
            return;
        }
        
        var_dump($data);

        $this->ageMessage = null;

        $this->addFlash('success', 'Vous êtes inscrit à la newsletter !');
    }
}
