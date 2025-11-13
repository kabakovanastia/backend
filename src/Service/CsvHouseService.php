<?php

namespace App\Service;

class CsvHouseService
{
    public function __construct(private string $dataDir)
    {
    }

    public function getAllHouses(): array
    {
        $file = $this->dataDir . '/houses.csv';
        if (!file_exists($file)) {
            return [];
        }

        $houses = [];
        $handle = fopen($file, 'r');
        $headers = fgetcsv($handle);
        while (($row = fgetcsv($handle)) !== false) {
            $houses[] = array_combine($headers, $row);
        }
        fclose($handle);
        return $houses;
    }

    public function getHouseById(string $id): ?array
    {
        foreach ($this->getAllHouses() as $house) {
            if ($house['id'] === $id) {
                return $house;
            }
        }
        return null;
    }
}
