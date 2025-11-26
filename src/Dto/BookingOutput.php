<?php

namespace App\Dto;

use ApiPlatform\Metadata as Api;

#[Api\ApiResource(
    operations: [
        new Api\Get(
            uriTemplate: '/api/bookings/{id}',
            provider: 'App\Provider\BookingProvider::getBooking'
        ),
        new Api\Put(
            uriTemplate: '/api/bookings/{id}',
            input: BookingUpdateInput::class,
            output: self::class,
            processor: 'App\Processor\BookingUpdateProcessor::process'
        ),
    ],
    shortName: 'Booking'
)]
class BookingOutput
{
    public int $id;
    public int $house_id;
    public int $user_id;
    public string $comment;
    public string $status;
}