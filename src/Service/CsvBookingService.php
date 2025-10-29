<?php

namespace App\Service;

class CsvBookingService
{
    private string $filePath;

    public function __construct(string $dataDir)
    {
        $this->filePath = $dataDir . '/bookings.csv';
        $this->ensureFileExists();
    }

    private function ensureFileExists(): void
    {
        if (!file_exists($this->filePath)) {
            $handle = fopen($this->filePath, 'w');
            fputcsv($handle, ['id', 'house_id', 'phone', 'comment', 'created_at']);
            fclose($handle);
        }
    }

    public function getAllBookings(): array
    {
        $bookings = [];
        $handle = fopen($this->filePath, 'r');
        $headers = fgetcsv($handle);
        while (($row = fgetcsv($handle)) !== false) {
            $bookings[] = array_combine($headers, $row);
        }
        fclose($handle);
        return $bookings;
    }

    public function createBooking(string $houseId, string $phone, string $comment): void
    {
        $bookings = $this->getAllBookings();
        $newId = empty($bookings) ? 1 : ((int) end($bookings)['id']) + 1;

        $handle = fopen($this->filePath, 'a');
        fputcsv($handle, [
            $newId,
            $houseId,
            $phone,
            $comment,
            (new \DateTime())->format('Y-m-d H:i:s')
        ]);
        fclose($handle);
    }

    public function updateBooking(int $id, string $newComment): bool
    {
        $bookings = $this->getAllBookings();
        $found = false;

        foreach ($bookings as &$booking) {
            if ((int)$booking['id'] === $id) {
                $booking['comment'] = $newComment;
                $found = true;
            }
        }

        if (!$found) {
            return false;
        }

        $handle = fopen($this->filePath, 'w');
        fputcsv($handle, ['id', 'house_id', 'phone', 'comment', 'created_at']);
        foreach ($bookings as $booking) {
            fputcsv($handle, $booking);
        }
        fclose($handle);

        return true;
    }
}