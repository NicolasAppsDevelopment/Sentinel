<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241202155934 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE answer (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, text VARCHAR(255) NOT NULL, is_correct TINYINT(1) NOT NULL, number_of_times_selected INT NOT NULL, INDEX IDX_DADD4A251E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, quizz_id INT DEFAULT NULL, statement VARCHAR(255) NOT NULL, type INT NOT NULL, position INT NOT NULL, INDEX IDX_B6F7494EBA934BCD (quizz_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question_answer_user_quizz_attempt (id INT AUTO_INCREMENT NOT NULL, attempt_id INT DEFAULT NULL, question_id INT DEFAULT NULL, answer_id INT DEFAULT NULL, INDEX IDX_9B2D491B191BE6B (attempt_id), INDEX IDX_9B2D4911E27F6BF (question_id), INDEX IDX_9B2D491AA334807 (answer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quizz (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, created_date DATE NOT NULL, INDEX IDX_7C77973DF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, score INT NOT NULL, creation_date DATE NOT NULL, is_verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_quizz_attempt (id INT AUTO_INCREMENT NOT NULL, quizz_id INT DEFAULT NULL, user_id INT NOT NULL, finished TINYINT(1) NOT NULL, score INT NOT NULL, played_date DATE NOT NULL, INDEX IDX_9C8870E2BA934BCD (quizz_id), UNIQUE INDEX UNIQ_9C8870E2A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A251E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494EBA934BCD FOREIGN KEY (quizz_id) REFERENCES quizz (id)');
        $this->addSql('ALTER TABLE question_answer_user_quizz_attempt ADD CONSTRAINT FK_9B2D491B191BE6B FOREIGN KEY (attempt_id) REFERENCES user_quizz_attempt (id)');
        $this->addSql('ALTER TABLE question_answer_user_quizz_attempt ADD CONSTRAINT FK_9B2D4911E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE question_answer_user_quizz_attempt ADD CONSTRAINT FK_9B2D491AA334807 FOREIGN KEY (answer_id) REFERENCES answer (id)');
        $this->addSql('ALTER TABLE quizz ADD CONSTRAINT FK_7C77973DF675F31B FOREIGN KEY (author_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user_quizz_attempt ADD CONSTRAINT FK_9C8870E2BA934BCD FOREIGN KEY (quizz_id) REFERENCES quizz (id)');
        $this->addSql('ALTER TABLE user_quizz_attempt ADD CONSTRAINT FK_9C8870E2A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A251E27F6BF');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494EBA934BCD');
        $this->addSql('ALTER TABLE question_answer_user_quizz_attempt DROP FOREIGN KEY FK_9B2D491B191BE6B');
        $this->addSql('ALTER TABLE question_answer_user_quizz_attempt DROP FOREIGN KEY FK_9B2D4911E27F6BF');
        $this->addSql('ALTER TABLE question_answer_user_quizz_attempt DROP FOREIGN KEY FK_9B2D491AA334807');
        $this->addSql('ALTER TABLE quizz DROP FOREIGN KEY FK_7C77973DF675F31B');
        $this->addSql('ALTER TABLE user_quizz_attempt DROP FOREIGN KEY FK_9C8870E2BA934BCD');
        $this->addSql('ALTER TABLE user_quizz_attempt DROP FOREIGN KEY FK_9C8870E2A76ED395');
        $this->addSql('DROP TABLE answer');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE question_answer_user_quizz_attempt');
        $this->addSql('DROP TABLE quizz');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE user_quizz_attempt');
    }
}
