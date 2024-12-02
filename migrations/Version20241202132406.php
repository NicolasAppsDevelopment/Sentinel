<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241202132406 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE question DROP answer_id1');
        $this->addSql('ALTER TABLE user_quizz_attempt ADD quizz_id INT DEFAULT NULL, DROP quiz_id');
        $this->addSql('ALTER TABLE user_quizz_attempt ADD CONSTRAINT FK_9C8870E2BA934BCD FOREIGN KEY (quizz_id) REFERENCES quizz (id)');
        $this->addSql('ALTER TABLE user_quizz_attempt ADD CONSTRAINT FK_9C8870E2A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_9C8870E2BA934BCD ON user_quizz_attempt (quizz_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9C8870E2A76ED395 ON user_quizz_attempt (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_quizz_attempt DROP FOREIGN KEY FK_9C8870E2BA934BCD');
        $this->addSql('ALTER TABLE user_quizz_attempt DROP FOREIGN KEY FK_9C8870E2A76ED395');
        $this->addSql('DROP INDEX IDX_9C8870E2BA934BCD ON user_quizz_attempt');
        $this->addSql('DROP INDEX UNIQ_9C8870E2A76ED395 ON user_quizz_attempt');
        $this->addSql('ALTER TABLE user_quizz_attempt ADD quiz_id INT NOT NULL, DROP quizz_id');
        $this->addSql('ALTER TABLE question ADD answer_id1 INT NOT NULL');
    }
}
