<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260529103826 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
    $this->addSql('DELETE s FROM contact s LEFT JOIN user u ON s.id_user_id = u.id WHERE u.id IS NULL');

    $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_contact_user FOREIGN KEY (id_user_id) REFERENCES user(id) ON DELETE CASCADE');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
