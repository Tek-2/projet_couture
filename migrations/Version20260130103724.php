<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260130103724 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE modele_location (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, prix_location NUMERIC(10, 2) NOT NULL, prix_achat NUMERIC(10, 2) DEFAULT NULL, tissu VARCHAR(255) NOT NULL, silhouette VARCHAR(255) NOT NULL, longueur VARCHAR(255) NOT NULL, longueur_manches VARCHAR(255) NOT NULL, forme_col VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, entretien_id INT NOT NULL, type_id INT NOT NULL, INDEX IDX_3646B7C9548DCEA2 (entretien_id), INDEX IDX_3646B7C9C54C8C93 (type_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE stock_location (id INT AUTO_INCREMENT NOT NULL, taille VARCHAR(255) NOT NULL, quantite INT NOT NULL, image_variante VARCHAR(255) NOT NULL, couleur_id INT NOT NULL, modele_location_id INT NOT NULL, INDEX IDX_1158DD89C31BA576 (couleur_id), INDEX IDX_1158DD89CC04B413 (modele_location_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE modele_location ADD CONSTRAINT FK_3646B7C9548DCEA2 FOREIGN KEY (entretien_id) REFERENCES entretien (id)');
        $this->addSql('ALTER TABLE modele_location ADD CONSTRAINT FK_3646B7C9C54C8C93 FOREIGN KEY (type_id) REFERENCES type_location (id)');
        $this->addSql('ALTER TABLE stock_location ADD CONSTRAINT FK_1158DD89C31BA576 FOREIGN KEY (couleur_id) REFERENCES couleur (id)');
        $this->addSql('ALTER TABLE stock_location ADD CONSTRAINT FK_1158DD89CC04B413 FOREIGN KEY (modele_location_id) REFERENCES modele_location (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE modele_location DROP FOREIGN KEY FK_3646B7C9548DCEA2');
        $this->addSql('ALTER TABLE modele_location DROP FOREIGN KEY FK_3646B7C9C54C8C93');
        $this->addSql('ALTER TABLE stock_location DROP FOREIGN KEY FK_1158DD89C31BA576');
        $this->addSql('ALTER TABLE stock_location DROP FOREIGN KEY FK_1158DD89CC04B413');
        $this->addSql('DROP TABLE modele_location');
        $this->addSql('DROP TABLE stock_location');
    }
}
