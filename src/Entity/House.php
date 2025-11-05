<?php

namespace App\Entity;

class House
{
    public function __construct(
        public int $id,
        public string $name,
        public bool $available = true
    ) {}
}