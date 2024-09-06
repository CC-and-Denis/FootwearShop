<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240902133600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, seller_username_id INT NOT NULL, carted_by_id INT DEFAULT NULL, model VARCHAR(255) NOT NULL, color VARCHAR(255) NOT NULL, material VARCHAR(255) DEFAULT NULL, brand VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, gender VARCHAR(10) DEFAULT NULL, for_kids TINYINT(1) DEFAULT NULL, price DOUBLE PRECISION NOT NULL, description LONGTEXT NOT NULL, quantity INT NOT NULL, views INT NOT NULL, items_sold INT NOT NULL, main_image VARCHAR(255) NOT NULL, other_images LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_D34A04AD40DEB3DF (seller_username_id), INDEX IDX_D34A04AD9C5F5D01 (carted_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD40DEB3DF FOREIGN KEY (seller_username_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD9C5F5D01 FOREIGN KEY (carted_by_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD40DEB3DF');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD9C5F5D01');
        $this->addSql('DROP TABLE product');
    }
}
