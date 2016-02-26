<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160217122908 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE email_notification ADD from_id INT DEFAULT NULL, CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE email_notification ADD CONSTRAINT FK_EA47909978CED90B FOREIGN KEY (from_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_EA47909978CED90B ON email_notification (from_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE email_notification DROP FOREIGN KEY FK_EA47909978CED90B');
        $this->addSql('DROP INDEX IDX_EA47909978CED90B ON email_notification');
        $this->addSql('ALTER TABLE email_notification DROP from_id, CHANGE user_id user_id INT DEFAULT NULL');
    }
}
