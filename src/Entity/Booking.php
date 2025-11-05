<?php

namespace App\Entity;

class Booking
{
    public function __construct(
        public int $id,
        public int $houseId,
        public int $userId,
        public string $comment = ''
    ) {}
}