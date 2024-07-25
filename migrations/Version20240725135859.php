<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240725135859 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, seller_username_id INT NOT NULL, model VARCHAR(255) NOT NULL, colors LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', materials LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', brand VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, gender VARCHAR(10) DEFAULT NULL, for_kids TINYINT(1) DEFAULT NULL, price DOUBLE PRECISION NOT NULL, description VARCHAR(255) NOT NULL, quantity INT NOT NULL, views INT NOT NULL, items_sold INT NOT NULL, INDEX IDX_D34A04AD40DEB3DF (seller_username_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD40DEB3DF FOREIGN KEY (seller_username_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD40DEB3DF');
        $this->addSql('DROP TABLE product');
    }
}
