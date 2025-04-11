<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250409091246 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE device ADD mac_address VARCHAR(17) NOT NULL, DROP mac_adress, CHANGE ip ip VARCHAR(15) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_92FB68EB728E969 ON device (mac_address)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_92FB68EA5E3B32D ON device (ip)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_92FB68EB728E969 ON device');
        $this->addSql('DROP INDEX UNIQ_92FB68EA5E3B32D ON device');
        $this->addSql('ALTER TABLE device ADD mac_adress VARCHAR(40) NOT NULL, DROP mac_address, CHANGE ip ip VARCHAR(40) NOT NULL');
    }
}
