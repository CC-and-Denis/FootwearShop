<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240920125610 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rating (id INT AUTO_INCREMENT NOT NULL, buyer_id INT NOT NULL, vendor_id INT NOT NULL, rated_product_id INT NOT NULL, score INT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_D88926226C755722 (buyer_id), INDEX IDX_D8892622F603EE73 (vendor_id), INDEX IDX_D8892622C9BB668E (rated_product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D88926226C755722 FOREIGN KEY (buyer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D8892622F603EE73 FOREIGN KEY (vendor_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D8892622C9BB668E FOREIGN KEY (rated_product_id) REFERENCES product (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rating DROP FOREIGN KEY FK_D88926226C755722');
        $this->addSql('ALTER TABLE rating DROP FOREIGN KEY FK_D8892622F603EE73');
        $this->addSql('ALTER TABLE rating DROP FOREIGN KEY FK_D8892622C9BB668E');
        $this->addSql('DROP TABLE rating');
    }
}
