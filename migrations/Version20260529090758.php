<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260529090758 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
    $this->addSql('DELETE s FROM stock_location s LEFT JOIN couleur c ON s.couleur_id = c.id WHERE c.id IS NULL');

    $this->addSql('ALTER TABLE stock_location ADD CONSTRAINT FK_stock_location_couleur FOREIGN KEY (couleur_id) REFERENCES couleur (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
