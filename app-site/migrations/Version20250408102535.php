<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250408102535 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE detections DROP FOREIGN KEY FK_69039D7F1D8292BE');
        $this->addSql('DROP INDEX IDX_69039D7F1D8292BE ON detections');
        $this->addSql('ALTER TABLE detections CHANGE couple_id_id couple_id INT NOT NULL');
        $this->addSql('ALTER TABLE detections ADD CONSTRAINT FK_69039D7FF66468CA FOREIGN KEY (couple_id) REFERENCES couple (id)');
        $this->addSql('CREATE INDEX IDX_69039D7FF66468CA ON detections (couple_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE detections DROP FOREIGN KEY FK_69039D7FF66468CA');
        $this->addSql('DROP INDEX IDX_69039D7FF66468CA ON detections');
        $this->addSql('ALTER TABLE detections CHANGE couple_id couple_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE detections ADD CONSTRAINT FK_69039D7F1D8292BE FOREIGN KEY (couple_id_id) REFERENCES couple (id)');
        $this->addSql('CREATE INDEX IDX_69039D7F1D8292BE ON detections (couple_id_id)');
    }
}
