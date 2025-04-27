<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250423201654 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE couple ADD last_detection_seek_date DATETIME NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detections DROP FOREIGN KEY FK_69039D7FF66468CA
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detections CHANGE couple_id couple_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detections ADD CONSTRAINT FK_69039D7FF66468CA FOREIGN KEY (couple_id) REFERENCES couple (id) ON DELETE SET NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE couple DROP last_detection_seek_date
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detections DROP FOREIGN KEY FK_69039D7FF66468CA
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detections CHANGE couple_id couple_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detections ADD CONSTRAINT FK_69039D7FF66468CA FOREIGN KEY (couple_id) REFERENCES couple (id)
        SQL);
    }
}
