<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180715141619 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notification CHANGE url url VARCHAR(255) DEFAULT NULL, CHANGE url_id url_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD settings JSON DEFAULT NULL, CHANGE username username VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notification CHANGE url url VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE url_id url_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user DROP settings, CHANGE username username VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci');
    }
}
