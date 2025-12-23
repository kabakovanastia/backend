<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251221000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create "user" table and import initial data from user.csv';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE "user" (
                id INT NOT NULL PRIMARY KEY,
                phone VARCHAR(20) NOT NULL UNIQUE,
                name VARCHAR(255) NOT NULL,
                password VARCHAR(255) DEFAULT NULL,
                roles JSON NOT NULL
            )
        ');

        $this->addSql(
            'INSERT INTO "user" (id, phone, name, password, roles) VALUES (?, ?, ?, ?, ?::JSON)',
            [
                1,
                '+79991234567',
                'Test User',
                null,
                '[]'
            ]
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE "user"');
    }
}