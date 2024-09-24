<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240924071120 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rating CHANGE rating score INT NOT NULL');
        $this->addSql('ALTER TABLE user DROP is_vendor, CHANGE postal_address postal_address VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rating CHANGE score rating INT NOT NULL');
        $this->addSql('ALTER TABLE user ADD is_vendor TINYINT(1) NOT NULL, CHANGE postal_address postal_address VARCHAR(255) NOT NULL');
    }
}
