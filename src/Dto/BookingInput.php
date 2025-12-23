<?php

namespace App\Dto;

use ApiPlatform\Metadata as Api;
use Symfony\Component\Validator\Constraints as Assert;

#[Api\ApiResource(
    operations: [
        new Api\Post(
            uriTemplate: '/api/bookings',
            input: self::class,
            output: BookingOutput::class,
            processor: 'App\Processor\BookingProcessor::process'
        ),
    ],
    shortName: 'Booking'
)]
class BookingInput
{
    #[Assert\NotBlank]
    #[Assert\Type('integer')]
    public int $house_id;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Regex(pattern: '/^\+7\d{10}$/')]
    public string $phone;

    #[Assert\Type('string')]
    public string $comment = '';
}