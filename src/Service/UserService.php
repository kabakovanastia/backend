<?php

namespace App\Service;

use App\Entity\User;

class UserService
{
    private string $filePath;
    private int $nextId = 1;

    public function __construct(string $userCsvPath)
    {
        $this->filePath = $userCsvPath;
        $this->loadNextId();
    }

    private function loadNextId(): void
    {
        if (!file_exists($this->filePath)) {
            return;
        }
        $lines = file($this->filePath, FILE_IGNORE_NEW_LINES);
        if (count($lines) <= 1) {
            return;
        }
        $last = end($lines);
        $lastId = (int) explode(',', $last)[0];
        $this->nextId = $lastId + 1;
    }

    public function createUser(string $phone, string $name = ''): User
    {
        $user = new User($this->nextId++, $phone, $name);
        $this->saveUser($user);
        return $user;
    }

    private function saveUser(User $user): void
    {
        $line = implode(',', [
            $user->id,
            '"' . str_replace('"', '""', $user->phone) . '"',
            '"' . str_replace('"', '""', $user->name) . '"'
        ]) . PHP_EOL;

        if (!file_exists($this->filePath)) {
            file_put_contents($this->filePath, "id,phone,name\n");
        }
        file_put_contents($this->filePath, $line, FILE_APPEND);
    }

    public function findUserByPhone(string $phone): ?User
    {
        if (!file_exists($this->filePath)) {
            return null;
        }

        $lines = file($this->filePath, FILE_IGNORE_NEW_LINES);
        array_shift($lines); // skip header

        foreach ($lines as $line) {
            $parts = str_getcsv($line);
            if ($parts[1] === $phone) {
                return new User((int)$parts[0], $parts[1], $parts[2] ?? '');
            }
        }

        return null;
    }
}
