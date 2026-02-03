<?php

namespace App\Service;

final class AgeChecker
{
    public function __construct(private readonly int $minAge = 16) {}

    public function isAgeOk(\DateTimeImmutable $birthDate, ?\DateImmutable $at = null): bool
    {
        $at ??= new \DateTimeImmutable('today');
        return $birthDate->modify(sprintf('+%d years', $this->minAge)) <= $at;
    }

    public function minAge(): int
    {
        return $this->minAge;
    }
}