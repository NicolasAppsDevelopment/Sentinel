<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250427211758 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE couple DROP FOREIGN KEY FK_D840B54930A2D4C9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE couple DROP FOREIGN KEY FK_D840B5494C952238
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE couple ADD CONSTRAINT FK_D840B54930A2D4C9 FOREIGN KEY (action_device_id) REFERENCES device (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE couple ADD CONSTRAINT FK_D840B5494C952238 FOREIGN KEY (camera_device_id) REFERENCES device (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detection DROP FOREIGN KEY FK_A35F1C6F66468CA
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detection ADD CONSTRAINT FK_A35F1C6F66468CA FOREIGN KEY (couple_id) REFERENCES couple (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE couple DROP FOREIGN KEY FK_D840B54930A2D4C9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE couple DROP FOREIGN KEY FK_D840B5494C952238
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE couple ADD CONSTRAINT FK_D840B54930A2D4C9 FOREIGN KEY (action_device_id) REFERENCES device (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE couple ADD CONSTRAINT FK_D840B5494C952238 FOREIGN KEY (camera_device_id) REFERENCES device (id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detection DROP FOREIGN KEY FK_A35F1C6F66468CA
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE detection ADD CONSTRAINT FK_A35F1C6F66468CA FOREIGN KEY (couple_id) REFERENCES couple (id) ON DELETE SET NULL
        SQL);
    }
}
