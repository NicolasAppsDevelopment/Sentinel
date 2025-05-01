<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250501111742 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE setting (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, monday_from TIME DEFAULT NULL, monday_to TIME DEFAULT NULL, tuesday_from TIME DEFAULT NULL, tuesday_to TIME DEFAULT NULL, wednesday_from TIME DEFAULT NULL, wednesday_to TIME DEFAULT NULL, thursday_from TIME DEFAULT NULL, thursday_to TIME DEFAULT NULL, friday_from TIME DEFAULT NULL, friday_to TIME DEFAULT NULL, saturday_from TIME DEFAULT NULL, saturday_to TIME DEFAULT NULL, sunday_from TIME DEFAULT NULL, sunday_to TIME DEFAULT NULL, UNIQUE INDEX UNIQ_9F74B898A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE setting ADD CONSTRAINT FK_9F74B898A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE setting DROP FOREIGN KEY FK_9F74B898A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE setting
        SQL);
    }
}
