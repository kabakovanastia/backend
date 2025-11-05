<?php

namespace App\Service;

use App\Entity\Booking;

class CsvBookingService
{
    private string $filePath;
    private int $nextId = 1;

    private function loadNextId(): void
    {
        if (!file_exists($this->filePath)) return;
        $lines = file($this->filePath, FILE_IGNORE_NEW_LINES);
        if (count($lines) <= 1) return;
        $last = end($lines);
        $lastId = (int) explode(',', $last)[0];
        $this->nextId = $lastId + 1;
    }

    public function createBooking(int $houseId, int $userId, string $comment = ''): Booking
    {
        $booking = new Booking($this->nextId++, $houseId, $userId, $comment);
        $this->saveBooking($booking);
        return $booking;
    }

    private function saveBooking(Booking $booking): void
    {
        $line = implode(',', [
            $booking->id,
            $booking->houseId,
            $booking->userId,
            '"' . str_replace('"', '""', $booking->comment) . '"'
        ]) . PHP_EOL;

        if (!file_exists($this->filePath)) {
            file_put_contents($this->filePath, "id,house_id,user_id,comment\n");
        }
        file_put_contents($this->filePath, $line, FILE_APPEND);
    }

    public function updateBooking(int $id, string $comment): bool
    {
        if (!file_exists($this->filePath)) return false;

        $lines = file($this->filePath, FILE_IGNORE_NEW_LINES);
        $header = array_shift($lines);
        $updated = false;

        foreach ($lines as $i => $line) {
            $parts = str_getcsv($line);
            if ((int)$parts[0] === $id) {
                $parts[3] = $comment;
                $lines[$i] = implode(',', array_map(fn($v) => '"' . str_replace('"', '""', $v) . '"', $parts));
                $updated = true;
                break;
            }
        }

        if ($updated) {
            file_put_contents($this->filePath, $header . PHP_EOL . implode(PHP_EOL, $lines) . PHP_EOL);
        }

        return $updated;
    }
}