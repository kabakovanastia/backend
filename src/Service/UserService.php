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

    public function createUser(string $phone, string $name = '', string $hashedPassword = ''): User
    {
        $user = new User($this->nextId++, $phone, $name, [], $hashedPassword);
        $this->saveUser($user);
        return $user;
    }

    private function saveUser(User $user): void
    {
        $line = implode(',', [
            $user->id,
            '"' . str_replace('"', '""', $user->phone) . '"',
            '"' . str_replace('"', '""', $user->name) . '"',
            '"' . str_replace('"', '""', $user->getPassword()) . '"'
        ]) . PHP_EOL;

        if (!file_exists($this->filePath)) {
            file_put_contents($this->filePath, "id,phone,name,password\n");
        }
        file_put_contents($this->filePath, $line, FILE_APPEND);
    }

    public function findUserByPhone(string $phone): ?User
    {
        $user = null;

        if (file_exists($this->filePath)) {
            $lines = file($this->filePath, FILE_IGNORE_NEW_LINES);
            $headerLine = array_shift($lines);

            if ($headerLine !== null) {
                $headers = str_getcsv($headerLine);
                $hasPasswordColumn = in_array('password', $headers);

                foreach ($lines as $line) {
                    $parts = str_getcsv($line);

                    if (!$hasPasswordColumn) {
                        if (count($parts) < 4) {
                            $parts = array_pad($parts, 4, '');
                        }
                        if ($parts[1] === $phone) {
                            $user = new User((int)$parts[0], $parts[1], $parts[2] ?? '', [], $parts[3] ?? '');
                            break;
                        }
                    } else {
                        if (count($parts) >= 4 && $parts[1] === $phone) {
                            $user = new User((int)$parts[0], $parts[1], $parts[2] ?? '', [], $parts[3]);
                            break;
                        }
                    }
                }
            }
        }

        return $user;
    }

    public function updateUserPassword(int $userId, string $hashedPassword): bool
    {
        $result = false;

        if (file_exists($this->filePath)) {
            $lines = file($this->filePath, FILE_IGNORE_NEW_LINES);
            $header = array_shift($lines);

            foreach ($lines as $i => $line) {
                $parts = str_getcsv($line);
                if (count($parts) >= 4 && (int)$parts[0] === $userId) {
                    $parts[3] = $hashedPassword;
                    $updatedLine = '"' . str_replace('"', '""', $parts[0]) . '","'
                                . str_replace('"', '""', $parts[1]) . '","'
                                . str_replace('"', '""', $parts[2]) . '","'
                                . str_replace('"', '""', $parts[3]) . '"';
                    $lines[$i] = $updatedLine;
                    $result = true;
                    break;
                }
            }

            if ($result) {
                file_put_contents($this->filePath, $header . PHP_EOL . implode(PHP_EOL, $lines) . PHP_EOL);
            }
        }

        return $result;
    }
}