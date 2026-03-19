<?php

namespace App\Repository\Abstract;

interface AbstractEntityRepositoryInterface
{
    public function flush(): void;
}