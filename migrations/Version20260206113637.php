<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260206113637 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande_location ADD id_user_id INT NOT NULL');
        $this->addSql('ALTER TABLE commande_location ADD CONSTRAINT FK_88BE915179F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_88BE915179F37AE5 ON commande_location (id_user_id)');
        $this->addSql('ALTER TABLE contact ADD id_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E63879F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_4C62E63879F37AE5 ON contact (id_user_id)');
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
        $this->addSql('ALTER TABLE commande_location DROP FOREIGN KEY FK_88BE915179F37AE5');
        $this->addSql('DROP INDEX IDX_88BE915179F37AE5 ON commande_location');
        $this->addSql('ALTER TABLE commande_location DROP id_user_id');
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E63879F37AE5');
        $this->addSql('DROP INDEX IDX_4C62E63879F37AE5 ON contact');
        $this->addSql('ALTER TABLE contact DROP id_user_id');
        $this->addSql('ALTER TABLE ligne_commande_location DROP FOREIGN KEY FK_D3E48AECEB334488');
        $this->addSql('ALTER TABLE ligne_commande_location DROP FOREIGN KEY FK_D3E48AEC6B3FF74B');
        $this->addSql('ALTER TABLE modele_location DROP FOREIGN KEY FK_3646B7C9548DCEA2');
        $this->addSql('ALTER TABLE modele_location DROP FOREIGN KEY FK_3646B7C9C54C8C93');
        $this->addSql('ALTER TABLE stock_location DROP FOREIGN KEY FK_1158DD89C31BA576');
        $this->addSql('ALTER TABLE stock_location DROP FOREIGN KEY FK_1158DD89CC04B413');
    }
}
