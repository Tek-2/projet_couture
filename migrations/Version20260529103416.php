<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260529103416 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
    $this->addSql('DELETE s FROM modele_location s LEFT JOIN entretien c ON s.entretien_id = c.id WHERE c.id IS NULL');

    $this->addSql('ALTER TABLE modele_location ADD CONSTRAINT FK_modele_location_entretien FOREIGN KEY (entretien_id) REFERENCES entretien (id) ON DELETE CASCADE');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
