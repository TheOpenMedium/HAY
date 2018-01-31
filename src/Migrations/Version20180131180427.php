<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180131180427 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user CHANGE username username VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE status DROP FOREIGN KEY FK_7B00651C79F37AE5');
        $this->addSql('DROP INDEX IDX_7B00651C79F37AE5 ON status');
        $this->addSql('ALTER TABLE status CHANGE id_user_id id_user INT NOT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE status CHANGE id_user id_user_id INT NOT NULL');
        $this->addSql('ALTER TABLE status ADD CONSTRAINT FK_7B00651C79F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_7B00651C79F37AE5 ON status (id_user_id)');
        $this->addSql('ALTER TABLE user CHANGE username username VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci');
    }
}
