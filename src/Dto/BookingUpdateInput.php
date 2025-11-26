<?php

namespace App\Dto;

use ApiPlatform\Metadata as Api;
use Symfony\Component\Validator\Constraints as Assert;

class BookingUpdateInput
{
    #[Assert\Type('string')]
    public string $comment = '';
}