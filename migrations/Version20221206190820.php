<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221206190820 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE planning CHANGE team team LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE support ADD status VARCHAR(255) NOT NULL, CHANGE dumpster_id dumpster_id INT DEFAULT NULL, CHANGE fk_admin_id fk_admin_id INT NOT NULL');
        $this->addSql('ALTER TABLE user ADD latitude DOUBLE PRECISION DEFAULT NULL, ADD longitude DOUBLE PRECISION DEFAULT NULL, CHANGE roles roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE way CHANGE team team LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', CHANGE route_json route_json LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE planning CHANGE team team LONGTEXT NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE support DROP status, CHANGE dumpster_id dumpster_id INT NOT NULL, CHANGE fk_admin_id fk_admin_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `user` DROP latitude, DROP longitude, CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE way CHANGE team team LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE route_json route_json LONGTEXT NOT NULL COLLATE `utf8mb4_bin`');
    }
}
