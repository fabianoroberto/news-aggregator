<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210416162630 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article ADD cover_filename VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE comment ADD photo_filename VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD photo_filename VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP cover_filename');
        $this->addSql('ALTER TABLE comment DROP photo_filename');
        $this->addSql('ALTER TABLE user DROP photo_filename');
    }
}
