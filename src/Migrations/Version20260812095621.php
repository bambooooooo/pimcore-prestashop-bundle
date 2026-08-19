<?php

declare(strict_types=1);

namespace Bnix\PimcorePrestashopBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260812095621 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create table for external system references';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE bnix_external_reference (
            id INT AUTO_INCREMENT NOT NULL, object_id INT NOT NULL,
            system_name VARCHAR(255) NOT NULL, external_id VARCHAR(255) NOT NULL,
            created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL,
            hash VARCHAR(64) NOT NULL,
            hash_2 VARCHAR(64),
            hash_3 VARCHAR(64),
            hash_4 VARCHAR(64),
        PRIMARY KEY(id))
        DEFAULT CHARACTER SET UTF8MB4 COLLATE `utf8mb4_general_ci`
        ENGINE = InnoDB;');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS bnix_external_reference');
    }
}
