<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241202141613 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE question_answer_user_quizz_attempt CHANGE attempt_id attempt_id INT DEFAULT NULL, CHANGE question_id question_id INT DEFAULT NULL, CHANGE answer_id answer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE question_answer_user_quizz_attempt ADD CONSTRAINT FK_9B2D491B191BE6B FOREIGN KEY (attempt_id) REFERENCES user_quizz_attempt (id)');
        $this->addSql('ALTER TABLE question_answer_user_quizz_attempt ADD CONSTRAINT FK_9B2D4911E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE question_answer_user_quizz_attempt ADD CONSTRAINT FK_9B2D491AA334807 FOREIGN KEY (answer_id) REFERENCES answer (id)');
        $this->addSql('CREATE INDEX IDX_9B2D491B191BE6B ON question_answer_user_quizz_attempt (attempt_id)');
        $this->addSql('CREATE INDEX IDX_9B2D4911E27F6BF ON question_answer_user_quizz_attempt (question_id)');
        $this->addSql('CREATE INDEX IDX_9B2D491AA334807 ON question_answer_user_quizz_attempt (answer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE question_answer_user_quizz_attempt DROP FOREIGN KEY FK_9B2D491B191BE6B');
        $this->addSql('ALTER TABLE question_answer_user_quizz_attempt DROP FOREIGN KEY FK_9B2D4911E27F6BF');
        $this->addSql('ALTER TABLE question_answer_user_quizz_attempt DROP FOREIGN KEY FK_9B2D491AA334807');
        $this->addSql('DROP INDEX IDX_9B2D491B191BE6B ON question_answer_user_quizz_attempt');
        $this->addSql('DROP INDEX IDX_9B2D4911E27F6BF ON question_answer_user_quizz_attempt');
        $this->addSql('DROP INDEX IDX_9B2D491AA334807 ON question_answer_user_quizz_attempt');
        $this->addSql('ALTER TABLE question_answer_user_quizz_attempt CHANGE attempt_id attempt_id INT NOT NULL, CHANGE question_id question_id INT NOT NULL, CHANGE answer_id answer_id INT NOT NULL');
    }
}
