<?php

namespace App\Entity;

class User
{
    public function __construct(
        public int $id,
        public string $phone,
        public string $name = ''
    ) {}
}