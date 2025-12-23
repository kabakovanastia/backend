<?php

namespace App\Provider;

use App\Dto\House;
use App\Service\CsvHouseService;

class HouseProvider
{
    public function __construct(private CsvHouseService $houseService) {}

    public function getAvailableHouses(): array
    {
        $housesData = $this->houseService->getAllHouses();
        $houses = [];
        foreach ($housesData as $data) {
            $house = new House();
            $house->id = (int) $data['id'];
            $house->name = $data['name'] ?? 'Unknown';
            $house->address = $data['address'] ?? '';
            $house->isAvailable = ($data['isAvailable'] ?? 'true') === 'true';
            $houses[] = $house;
        }
        return $houses;
    }
}