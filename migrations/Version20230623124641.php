<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230623124641 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change foreign keys name from column_id_id to column_id';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment CHANGE trick_id_id trick_id integer NOT NULL');
        $this->addSql('ALTER TABLE comment CHANGE user_id_id user_id integer NOT NULL');
        $this->addSql('ALTER TABLE image CHANGE trick_id_id trick_id integer NOT NULL');
        $this->addSql('ALTER TABLE video CHANGE trick_id_id trick_id integer NOT NULL');
        $this->addSql('ALTER TABLE trick CHANGE user_id_id user_id integer NOT NULL');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment CHANGE trick_id trick_id_id integer NOT NULL');
        $this->addSql('ALTER TABLE comment CHANGE user_id user_id_id integer NOT NULL');
        $this->addSql('ALTER TABLE image CHANGE trick_id trick_id_id integer NOT NULL');
        $this->addSql('ALTER TABLE video CHANGE trick_id trick_id_id integer NOT NULL');
        $this->addSql('ALTER TABLE trick CHANGE user_id user_id_id integer NOT NULL');
    }
}
