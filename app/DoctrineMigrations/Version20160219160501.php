<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160219160501 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE invitations (id INT AUTO_INCREMENT NOT NULL, creator_id INT DEFAULT NULL, code VARCHAR(6) NOT NULL, email VARCHAR(256) NOT NULL, sent TINYINT(1) NOT NULL, accepted TINYINT(1) NOT NULL, created_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_232710AE77153098 (code), INDEX IDX_232710AE61220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE invitations ADD CONSTRAINT FK_232710AE61220EA6 FOREIGN KEY (creator_id) REFERENCES users (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE invitations');
    }
}
