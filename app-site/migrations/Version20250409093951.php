<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250409093951 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE couple DROP FOREIGN KEY FK_D840B54930A2D4C9');
        $this->addSql('ALTER TABLE couple DROP FOREIGN KEY FK_D840B5494C952238');
        $this->addSql('ALTER TABLE couple CHANGE action_device_id action_device_id INT DEFAULT NULL, CHANGE camera_device_id camera_device_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE couple ADD CONSTRAINT FK_D840B54930A2D4C9 FOREIGN KEY (action_device_id) REFERENCES device (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE couple ADD CONSTRAINT FK_D840B5494C952238 FOREIGN KEY (camera_device_id) REFERENCES device (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE couple DROP FOREIGN KEY FK_D840B54930A2D4C9');
        $this->addSql('ALTER TABLE couple DROP FOREIGN KEY FK_D840B5494C952238');
        $this->addSql('ALTER TABLE couple CHANGE action_device_id action_device_id INT NOT NULL, CHANGE camera_device_id camera_device_id INT NOT NULL');
        $this->addSql('ALTER TABLE couple ADD CONSTRAINT FK_D840B54930A2D4C9 FOREIGN KEY (action_device_id) REFERENCES device (id)');
        $this->addSql('ALTER TABLE couple ADD CONSTRAINT FK_D840B5494C952238 FOREIGN KEY (camera_device_id) REFERENCES device (id)');
    }
}
