<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260619111113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ligne_commande_location ADD CONSTRAINT FK_D3E48AECD655F98E FOREIGN KEY (commandeloc_id) REFERENCES commande_location (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ligne_commande_location ADD CONSTRAINT FK_D3E48AEC732D3ABC FOREIGN KEY (stockloc_id) REFERENCES stock_location (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_D3E48AECD655F98E ON ligne_commande_location (commandeloc_id)');
        $this->addSql('CREATE INDEX IDX_D3E48AEC732D3ABC ON ligne_commande_location (stockloc_id)');
        $this->addSql('ALTER TABLE stock_location_image ADD CONSTRAINT FK_1482F831CC04B413 FOREIGN KEY (modele_location_id) REFERENCES modele_location (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ligne_commande_location DROP FOREIGN KEY FK_D3E48AECD655F98E');
        $this->addSql('ALTER TABLE ligne_commande_location DROP FOREIGN KEY FK_D3E48AEC732D3ABC');
        $this->addSql('DROP INDEX IDX_D3E48AECD655F98E ON ligne_commande_location');
        $this->addSql('DROP INDEX IDX_D3E48AEC732D3ABC ON ligne_commande_location');
        $this->addSql('ALTER TABLE stock_location_image DROP FOREIGN KEY FK_1482F831CC04B413');
    }
}
