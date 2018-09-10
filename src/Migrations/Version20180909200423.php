<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180909200423 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE survey (id INT AUTO_INCREMENT NOT NULL, law_id INT DEFAULT NULL, report_id INT DEFAULT NULL, date DATETIME NOT NULL, question VARCHAR(255) NOT NULL, answers LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', expiration_date DATE DEFAULT NULL, role VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_AD5F9BFC54EB478 (law_id), UNIQUE INDEX UNIQ_AD5F9BFC4BD2A4C0 (report_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE survey_post (survey_id INT NOT NULL, post_id INT NOT NULL, INDEX IDX_9C636C46B3FE509D (survey_id), INDEX IDX_9C636C464B89032C (post_id), PRIMARY KEY(survey_id, post_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE survey_comment (survey_id INT NOT NULL, comment_id INT NOT NULL, INDEX IDX_D05871D7B3FE509D (survey_id), INDEX IDX_D05871D7F8697D13 (comment_id), PRIMARY KEY(survey_id, comment_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE survey ADD CONSTRAINT FK_AD5F9BFC54EB478 FOREIGN KEY (law_id) REFERENCES laws (id)');
        $this->addSql('ALTER TABLE survey ADD CONSTRAINT FK_AD5F9BFC4BD2A4C0 FOREIGN KEY (report_id) REFERENCES report (id)');
        $this->addSql('ALTER TABLE survey_post ADD CONSTRAINT FK_9C636C46B3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE survey_post ADD CONSTRAINT FK_9C636C464B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE survey_comment ADD CONSTRAINT FK_D05871D7B3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE survey_comment ADD CONSTRAINT FK_D05871D7F8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification CHANGE url url VARCHAR(255) DEFAULT NULL, CHANGE url_id url_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE username username VARCHAR(255) DEFAULT NULL, CHANGE settings settings JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE report CHANGE reported_post_id reported_post_id INT DEFAULT NULL, CHANGE reported_comment_id reported_comment_id INT DEFAULT NULL, CHANGE validated validated TINYINT(1) DEFAULT NULL, CHANGE punishment punishment VARCHAR(255) DEFAULT NULL, CHANGE punishment_expiration_date punishment_expiration_date DATETIME DEFAULT NULL, CHANGE date_limit_contest date_limit_contest DATETIME DEFAULT NULL, CHANGE contested contested TINYINT(1) DEFAULT NULL, CHANGE contest_result contest_result TINYINT(1) DEFAULT NULL, CHANGE needhelp needhelp TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE survey_post DROP FOREIGN KEY FK_9C636C46B3FE509D');
        $this->addSql('ALTER TABLE survey_comment DROP FOREIGN KEY FK_D05871D7B3FE509D');
        $this->addSql('DROP TABLE survey');
        $this->addSql('DROP TABLE survey_post');
        $this->addSql('DROP TABLE survey_comment');
        $this->addSql('ALTER TABLE notification CHANGE url url VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE url_id url_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE report CHANGE reported_post_id reported_post_id INT DEFAULT NULL, CHANGE reported_comment_id reported_comment_id INT DEFAULT NULL, CHANGE validated validated TINYINT(1) DEFAULT \'NULL\', CHANGE punishment punishment VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE punishment_expiration_date punishment_expiration_date DATETIME DEFAULT \'NULL\', CHANGE needhelp needhelp TINYINT(1) DEFAULT \'NULL\', CHANGE date_limit_contest date_limit_contest DATETIME DEFAULT \'NULL\', CHANGE contested contested TINYINT(1) DEFAULT \'NULL\', CHANGE contest_result contest_result TINYINT(1) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user CHANGE username username VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE settings settings LONGTEXT DEFAULT NULL COLLATE utf8mb4_bin');
    }
}
