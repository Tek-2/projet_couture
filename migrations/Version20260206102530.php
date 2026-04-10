<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260206102530 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, sujet VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, date DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE ligne_commande_location ADD CONSTRAINT FK_D3E48AECEB334488 FOREIGN KEY (id_commandeloc_id) REFERENCES commande_location (id)');
        $this->addSql('ALTER TABLE ligne_commande_location ADD CONSTRAINT FK_D3E48AEC6B3FF74B FOREIGN KEY (id_stockloc_id) REFERENCES stock_location (id)');
        $this->addSql('ALTER TABLE modele_location ADD CONSTRAINT FK_3646B7C9548DCEA2 FOREIGN KEY (entretien_id) REFERENCES entretien (id)');
        $this->addSql('ALTER TABLE modele_location ADD CONSTRAINT FK_3646B7C9C54C8C93 FOREIGN KEY (type_id) REFERENCES type_location (id)');
        $this->addSql('ALTER TABLE stock_location ADD CONSTRAINT FK_1158DD89C31BA576 FOREIGN KEY (couleur_id) REFERENCES couleur (id)');
        $this->addSql('ALTER TABLE stock_location ADD CONSTRAINT FK_1158DD89CC04B413 FOREIGN KEY (modele_location_id) REFERENCES modele_location (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE contact');
        $this->addSql('ALTER TABLE ligne_commande_location DROP FOREIGN KEY FK_D3E48AECEB334488');
        $this->addSql('ALTER TABLE ligne_commande_location DROP FOREIGN KEY FK_D3E48AEC6B3FF74B');
        $this->addSql('ALTER TABLE modele_location DROP FOREIGN KEY FK_3646B7C9548DCEA2');
        $this->addSql('ALTER TABLE modele_location DROP FOREIGN KEY FK_3646B7C9C54C8C93');
        $this->addSql('ALTER TABLE stock_location DROP FOREIGN KEY FK_1158DD89C31BA576');
        $this->addSql('ALTER TABLE stock_location DROP FOREIGN KEY FK_1158DD89CC04B413');
    }
}
