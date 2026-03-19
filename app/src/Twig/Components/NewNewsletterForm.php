<?php

namespace App\Twig\Components;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\ValidatableComponentTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use App\Factory\NewsletterFactory;
use App\Repository\NewsletterRepository;
use App\Form\NewsletterType;
use App\Form\Model\NewsletterData;

#[AsLiveComponent]
final class NewNewsletterForm extends AbstractController
{
    use DefaultActionTrait;
    use ValidatableComponentTrait;
    use ComponentWithFormTrait;
    
    #[LiveProp]
    public ?NewsletterData $initialFormData = null;

    #[LiveProp]
    public ?bool $isSubmitted = false;

    public function __construct(
        private readonly NewsletterRepository $newsletterRepository,
    ) {
        $this->data = new NewsletterData();
    }

    protected function instantiateForm(): FormInterface
    {
        $this->isSubmitted = false;
        return $this->createForm(NewsletterType::class, $this->data);
    }

    #[LiveAction]
    public function save(): void 
    {

        $this->submitForm();  

        /** @var \App\Form\Model\NewsletterData $data */
        $data = $this->form->getData();

        $newsletter = $this->newsletterRepository->findOneBy(['name' => $data->name]);
        
        if($newsletter) {
            $this->addFlash('warning', 'La newsletter existe déjà !');
            return;
        }

        $newsletter = NewsletterFactory::findOrCreate(['name' => $data->name, 'description' => $data->description]);

        try {
            $this->newsletterRepository->add($newsletter);
        } catch (UniqueConstraintViolationException) {
            $newsletter = $this->newsletterRepository->findOneBy(['name' => $data->name]);
        }

        $this->isSubmitted = true;

        $this->addFlash('success', 'La newsletter a bien été créée !');
    }

    public function canSubmit(): bool
    {
        /** @var \App\Form\Model\NewsletterData $data */
        $data = $this->getForm()->getData();

        if (!$data->name) {
            return false;
        }

        if (!$data->description) {
            return false;
        }

        return true;
    }
}
