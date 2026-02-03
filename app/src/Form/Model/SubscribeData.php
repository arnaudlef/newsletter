<?php

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

final class SubscribeData
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;

    #[Assert\NotNull]
    #[Assert\LessThanOrEqual('today')]
    public ?\DateTimeImmutable $birthDate = null;

    /**
     * @var Collection<int, Newsletter>
     */
    #[Assert\Count(min: 1, minMessage: 'Sélectionne au moins une newsletter.')]
    public Collection $newsletters;

    public function __construct()
    {
        $this->newsletters = new ArrayCollection();
    }
}