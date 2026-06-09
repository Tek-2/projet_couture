<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260529104622 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
    $this->addSql('DELETE s FROM ligne_commande_location s LEFT JOIN commande_location c ON s.id_commandeloc_id = c.id WHERE c.id IS NULL');

    $this->addSql('ALTER TABLE ligne_commande_location ADD CONSTRAINT FK_ligne_commande_location_commande_location FOREIGN KEY (id_commandeloc_id) REFERENCES commande_location(id) ON DELETE CASCADE');

    $this->addSql('DELETE s FROM ligne_commande_location s LEFT JOIN stock_location c ON s.id_stockloc_id = c.id WHERE c.id IS NULL');

    $this->addSql('ALTER TABLE ligne_commande_location ADD CONSTRAINT FK_ligne_commande_location_stock_location FOREIGN KEY (id_stockloc_id) REFERENCES stock_location(id) ON DELETE CASCADE');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
