<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190530210356 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user CHANGE username username VARCHAR(255) DEFAULT NULL, CHANGE alt alt VARCHAR(255) DEFAULT NULL, CHANGE settings settings JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE report CHANGE reported_post_id reported_post_id INT DEFAULT NULL, CHANGE reported_comment_id reported_comment_id INT DEFAULT NULL, CHANGE validated validated TINYINT(1) DEFAULT NULL, CHANGE punishment punishment VARCHAR(255) DEFAULT NULL, CHANGE punishment_expiration_date punishment_expiration_date DATETIME DEFAULT NULL, CHANGE needhelp needhelp TINYINT(1) DEFAULT NULL, CHANGE date_limit_contest date_limit_contest DATETIME DEFAULT NULL, CHANGE contested contested TINYINT(1) DEFAULT NULL, CHANGE contest_result contest_result TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE survey CHANGE law_id law_id INT DEFAULT NULL, CHANGE report_id report_id INT DEFAULT NULL, CHANGE expiration_date expiration_date DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE notification CHANGE url url VARCHAR(255) DEFAULT NULL, CHANGE url_id url_id INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notification CHANGE url url VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE url_id url_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE report CHANGE reported_post_id reported_post_id INT DEFAULT NULL, CHANGE reported_comment_id reported_comment_id INT DEFAULT NULL, CHANGE validated validated TINYINT(1) DEFAULT \'NULL\', CHANGE punishment punishment VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE punishment_expiration_date punishment_expiration_date DATETIME DEFAULT \'NULL\', CHANGE needhelp needhelp TINYINT(1) DEFAULT \'NULL\', CHANGE date_limit_contest date_limit_contest DATETIME DEFAULT \'NULL\', CHANGE contested contested TINYINT(1) DEFAULT \'NULL\', CHANGE contest_result contest_result TINYINT(1) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE survey CHANGE law_id law_id INT DEFAULT NULL, CHANGE report_id report_id INT DEFAULT NULL, CHANGE expiration_date expiration_date DATE DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user CHANGE username username VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE alt alt VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE settings settings LONGTEXT DEFAULT NULL COLLATE utf8mb4_bin');
    }
}
