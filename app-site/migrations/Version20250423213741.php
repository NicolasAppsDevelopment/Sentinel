<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250423213741 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE detection (id INT AUTO_INCREMENT NOT NULL, couple_id INT DEFAULT NULL, image_filename VARCHAR(255) DEFAULT NULL, triggered_at DATETIME NOT NULL, INDEX IDX_A35F1C6F66468CA (couple_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detection ADD CONSTRAINT FK_A35F1C6F66468CA FOREIGN KEY (couple_id) REFERENCES couple (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detections DROP FOREIGN KEY FK_69039D7FF66468CA
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE detections
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE detections (id INT AUTO_INCREMENT NOT NULL, couple_id INT DEFAULT NULL, image_filename VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, triggered_at DATETIME NOT NULL, INDEX IDX_69039D7FF66468CA (couple_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detections ADD CONSTRAINT FK_69039D7FF66468CA FOREIGN KEY (couple_id) REFERENCES couple (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detection DROP FOREIGN KEY FK_A35F1C6F66468CA
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE detection
        SQL);
    }
}
