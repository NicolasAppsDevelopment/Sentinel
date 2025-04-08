<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250321105200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE couple (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, action_device_id INT NOT NULL, camera_device_id INT NOT NULL, title VARCHAR(255) NOT NULL, association_date DATETIME NOT NULL, enabled TINYINT(1) NOT NULL, INDEX IDX_D840B549A76ED395 (user_id), UNIQUE INDEX UNIQ_D840B54930A2D4C9 (action_device_id), UNIQUE INDEX UNIQ_D840B5494C952238 (camera_device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE detections (id INT AUTO_INCREMENT NOT NULL, couple_id_id INT NOT NULL, image_filename VARCHAR(255) DEFAULT NULL, triggered_at DATETIME NOT NULL, INDEX IDX_69039D7F1D8292BE (couple_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE device (id INT AUTO_INCREMENT NOT NULL, mac_adress VARCHAR(40) NOT NULL, ip VARCHAR(40) NOT NULL, is_camera TINYINT(1) NOT NULL, is_paired TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE couple ADD CONSTRAINT FK_D840B549A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE couple ADD CONSTRAINT FK_D840B54930A2D4C9 FOREIGN KEY (action_device_id) REFERENCES device (id)');
        $this->addSql('ALTER TABLE couple ADD CONSTRAINT FK_D840B5494C952238 FOREIGN KEY (camera_device_id) REFERENCES device (id)');
        $this->addSql('ALTER TABLE detections ADD CONSTRAINT FK_69039D7F1D8292BE FOREIGN KEY (couple_id_id) REFERENCES couple (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE couple DROP FOREIGN KEY FK_D840B549A76ED395');
        $this->addSql('ALTER TABLE couple DROP FOREIGN KEY FK_D840B54930A2D4C9');
        $this->addSql('ALTER TABLE couple DROP FOREIGN KEY FK_D840B5494C952238');
        $this->addSql('ALTER TABLE detections DROP FOREIGN KEY FK_69039D7F1D8292BE');
        $this->addSql('DROP TABLE couple');
        $this->addSql('DROP TABLE detections');
        $this->addSql('DROP TABLE device');
        $this->addSql('DROP TABLE `user`');
    }
}
