<?php

namespace App\Dto;

use ApiPlatform\Metadata as Api;

#[Api\ApiResource(
    operations: [
        new Api\GetCollection(
            uriTemplate: '/api/houses/available',
            read: false,
            provider: 'App\Provider\HouseProvider::getAvailableHouses'
        ),
    ],
    shortName: 'House',
    paginationEnabled: false
)]
class House
{
    public int $id;
    public string $name;
    public string $address;
    public bool $isAvailable;
}