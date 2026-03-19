<?php

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

final class NewsletterData
{
    #[Assert\NotBlank]
    public ?string $name = null;

    #[Assert\NotBlank]
    public ?string $description = null;
}