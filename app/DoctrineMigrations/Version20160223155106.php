<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160223155106 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE musicians_brands DROP FOREIGN KEY FK_10F06F0344F5D008');
        $this->addSql('CREATE TABLE musicians_gear (id INT AUTO_INCREMENT NOT NULL, musician_id INT NOT NULL, name VARCHAR(100) NOT NULL, position INT NOT NULL, INDEX IDX_252706789523AA8A (musician_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE musicians_gear ADD CONSTRAINT FK_252706789523AA8A FOREIGN KEY (musician_id) REFERENCES users (id)');
        $this->addSql('DROP TABLE brands');
        $this->addSql('DROP TABLE musicians_brands');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE brands (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(200) NOT NULL COLLATE utf8_unicode_ci, parent VARCHAR(200) NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE musicians_brands (id INT AUTO_INCREMENT NOT NULL, brand_id INT NOT NULL, musician_id INT NOT NULL, position INT NOT NULL, INDEX IDX_10F06F039523AA8A (musician_id), INDEX IDX_10F06F0344F5D008 (brand_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE musicians_brands ADD CONSTRAINT FK_10F06F0344F5D008 FOREIGN KEY (brand_id) REFERENCES brands (id)');
        $this->addSql('ALTER TABLE musicians_brands ADD CONSTRAINT FK_10F06F039523AA8A FOREIGN KEY (musician_id) REFERENCES users (id)');
        $this->addSql('DROP TABLE musicians_gear');
    }
}
