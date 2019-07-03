<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190701233816 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE report CHANGE reported_post_id reported_post_id VARCHAR(10) DEFAULT NULL, CHANGE reported_comment_id reported_comment_id VARCHAR(10) DEFAULT NULL, CHANGE validated validated TINYINT(1) DEFAULT NULL, CHANGE punishment punishment VARCHAR(255) DEFAULT NULL, CHANGE punishment_expiration_date punishment_expiration_date DATETIME DEFAULT NULL, CHANGE needhelp needhelp TINYINT(1) DEFAULT NULL, CHANGE date_limit_contest date_limit_contest DATETIME DEFAULT NULL, CHANGE contested contested TINYINT(1) DEFAULT NULL, CHANGE contest_result contest_result TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE survey CHANGE law_id law_id INT DEFAULT NULL, CHANGE report_id report_id INT DEFAULT NULL, CHANGE expiration_date expiration_date DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE notification CHANGE url url VARCHAR(255) DEFAULT NULL, CHANGE url_id url_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD child_name VARCHAR(255) DEFAULT NULL, CHANGE first_name first_name VARCHAR(255) DEFAULT NULL, CHANGE last_name last_name VARCHAR(255) DEFAULT NULL, CHANGE email email VARCHAR(255) DEFAULT NULL, CHANGE username username VARCHAR(255) DEFAULT NULL, CHANGE password password VARCHAR(255) DEFAULT NULL, CHANGE alt alt VARCHAR(255) DEFAULT NULL, CHANGE settings settings JSON DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notification CHANGE url url VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE url_id url_id VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE report CHANGE reported_post_id reported_post_id VARCHAR(10) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE reported_comment_id reported_comment_id VARCHAR(10) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE validated validated TINYINT(1) DEFAULT \'NULL\', CHANGE punishment punishment VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE punishment_expiration_date punishment_expiration_date DATETIME DEFAULT \'NULL\', CHANGE needhelp needhelp TINYINT(1) DEFAULT \'NULL\', CHANGE date_limit_contest date_limit_contest DATETIME DEFAULT \'NULL\', CHANGE contested contested TINYINT(1) DEFAULT \'NULL\', CHANGE contest_result contest_result TINYINT(1) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE survey CHANGE law_id law_id INT DEFAULT NULL, CHANGE report_id report_id INT DEFAULT NULL, CHANGE expiration_date expiration_date DATE DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user DROP child_name, CHANGE first_name first_name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE last_name last_name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE email email VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE username username VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE password password VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE alt alt VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE settings settings LONGTEXT DEFAULT NULL COLLATE utf8mb4_bin');
    }
}
