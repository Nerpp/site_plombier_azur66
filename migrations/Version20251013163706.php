<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251013163706 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE services ADD admin_service_id INT NOT NULL');
        $this->addSql('ALTER TABLE services ADD CONSTRAINT FK_7332E16925620F63 FOREIGN KEY (admin_service_id) REFERENCES `admin` (id)');
        $this->addSql('CREATE INDEX IDX_7332E16925620F63 ON services (admin_service_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE services DROP FOREIGN KEY FK_7332E16925620F63');
        $this->addSql('DROP INDEX IDX_7332E16925620F63 ON services');
        $this->addSql('ALTER TABLE services DROP admin_service_id');
    }
}
