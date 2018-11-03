<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180910232018 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE statistics (id INT AUTO_INCREMENT NOT NULL, date DATE NOT NULL, visits INT NOT NULL, requests INT NOT NULL, new_users INT NOT NULL, new_posts INT NOT NULL, new_comments INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, type INT NOT NULL, content LONGTEXT NOT NULL, date_send DATETIME NOT NULL, url VARCHAR(255) DEFAULT NULL, url_id INT DEFAULT NULL, INDEX IDX_BF5476CAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, post_id INT NOT NULL, comment LONGTEXT NOT NULL, date_send DATETIME NOT NULL, INDEX IDX_9474526CA76ED395 (user_id), INDEX IDX_9474526C4B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE friend_request (id INT AUTO_INCREMENT NOT NULL, from_user_id INT NOT NULL, to_user_id INT NOT NULL, INDEX IDX_F284D942130303A (from_user_id), INDEX IDX_F284D9429F6EE60 (to_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE laws (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, date DATETIME NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, INDEX IDX_B0D3F907A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, username VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, date_sign DATETIME NOT NULL, mail_conf TINYINT(1) NOT NULL, url VARCHAR(2000) NOT NULL, alt VARCHAR(255) NOT NULL, settings JSON DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_user (user_source INT NOT NULL, user_target INT NOT NULL, INDEX IDX_F7129A803AD8644E (user_source), INDEX IDX_F7129A80233D34C1 (user_target), PRIMARY KEY(user_source, user_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, content LONGTEXT NOT NULL, date_post DATETIME NOT NULL, color VARCHAR(3) NOT NULL, size VARCHAR(2) NOT NULL, font VARCHAR(2) NOT NULL, INDEX IDX_5A8A6C8DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report (id INT AUTO_INCREMENT NOT NULL, reporter_id INT NOT NULL, reported_user_id INT NOT NULL, reported_post_id INT DEFAULT NULL, reported_comment_id INT DEFAULT NULL, date DATETIME NOT NULL, law VARCHAR(255) NOT NULL, reporter_msg LONGTEXT DEFAULT NULL, emergency_level INT NOT NULL, validated TINYINT(1) DEFAULT NULL, punishment VARCHAR(255) DEFAULT NULL, punishment_expiration_date DATETIME DEFAULT NULL, moderator_msg LONGTEXT DEFAULT NULL, needhelp TINYINT(1) DEFAULT NULL, date_limit_contest DATETIME DEFAULT NULL, contested TINYINT(1) DEFAULT NULL, contest_result TINYINT(1) DEFAULT NULL, INDEX IDX_C42F7784E1CFE6F5 (reporter_id), INDEX IDX_C42F7784E7566E (reported_user_id), INDEX IDX_C42F7784EC0086D7 (reported_post_id), INDEX IDX_C42F77849368B60F (reported_comment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report_user (report_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_FEBF3BB24BD2A4C0 (report_id), INDEX IDX_FEBF3BB2A76ED395 (user_id), PRIMARY KEY(report_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE survey (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, law_id INT DEFAULT NULL, report_id INT DEFAULT NULL, date DATETIME NOT NULL, question VARCHAR(255) NOT NULL, answers LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', color LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', expiration_date DATE DEFAULT NULL, role VARCHAR(255) NOT NULL, INDEX IDX_AD5F9BFCA76ED395 (user_id), UNIQUE INDEX UNIQ_AD5F9BFC54EB478 (law_id), UNIQUE INDEX UNIQ_AD5F9BFC4BD2A4C0 (report_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE survey_post (survey_id INT NOT NULL, post_id INT NOT NULL, INDEX IDX_9C636C46B3FE509D (survey_id), INDEX IDX_9C636C464B89032C (post_id), PRIMARY KEY(survey_id, post_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE survey_comment (survey_id INT NOT NULL, comment_id INT NOT NULL, INDEX IDX_D05871D7B3FE509D (survey_id), INDEX IDX_D05871D7F8697D13 (comment_id), PRIMARY KEY(survey_id, comment_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C4B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE friend_request ADD CONSTRAINT FK_F284D942130303A FOREIGN KEY (from_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE friend_request ADD CONSTRAINT FK_F284D9429F6EE60 FOREIGN KEY (to_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE laws ADD CONSTRAINT FK_B0D3F907A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_F7129A803AD8644E FOREIGN KEY (user_source) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_F7129A80233D34C1 FOREIGN KEY (user_target) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784E1CFE6F5 FOREIGN KEY (reporter_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784E7566E FOREIGN KEY (reported_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784EC0086D7 FOREIGN KEY (reported_post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F77849368B60F FOREIGN KEY (reported_comment_id) REFERENCES comment (id)');
        $this->addSql('ALTER TABLE report_user ADD CONSTRAINT FK_FEBF3BB24BD2A4C0 FOREIGN KEY (report_id) REFERENCES report (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE report_user ADD CONSTRAINT FK_FEBF3BB2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE survey ADD CONSTRAINT FK_AD5F9BFCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE survey ADD CONSTRAINT FK_AD5F9BFC54EB478 FOREIGN KEY (law_id) REFERENCES laws (id)');
        $this->addSql('ALTER TABLE survey ADD CONSTRAINT FK_AD5F9BFC4BD2A4C0 FOREIGN KEY (report_id) REFERENCES report (id)');
        $this->addSql('ALTER TABLE survey_post ADD CONSTRAINT FK_9C636C46B3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE survey_post ADD CONSTRAINT FK_9C636C464B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE survey_comment ADD CONSTRAINT FK_D05871D7B3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE survey_comment ADD CONSTRAINT FK_D05871D7F8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F77849368B60F');
        $this->addSql('ALTER TABLE survey_comment DROP FOREIGN KEY FK_D05871D7F8697D13');
        $this->addSql('ALTER TABLE survey DROP FOREIGN KEY FK_AD5F9BFC54EB478');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA76ED395');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE friend_request DROP FOREIGN KEY FK_F284D942130303A');
        $this->addSql('ALTER TABLE friend_request DROP FOREIGN KEY FK_F284D9429F6EE60');
        $this->addSql('ALTER TABLE laws DROP FOREIGN KEY FK_B0D3F907A76ED395');
        $this->addSql('ALTER TABLE user_user DROP FOREIGN KEY FK_F7129A803AD8644E');
        $this->addSql('ALTER TABLE user_user DROP FOREIGN KEY FK_F7129A80233D34C1');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DA76ED395');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784E1CFE6F5');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784E7566E');
        $this->addSql('ALTER TABLE report_user DROP FOREIGN KEY FK_FEBF3BB2A76ED395');
        $this->addSql('ALTER TABLE survey DROP FOREIGN KEY FK_AD5F9BFCA76ED395');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C4B89032C');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784EC0086D7');
        $this->addSql('ALTER TABLE survey_post DROP FOREIGN KEY FK_9C636C464B89032C');
        $this->addSql('ALTER TABLE report_user DROP FOREIGN KEY FK_FEBF3BB24BD2A4C0');
        $this->addSql('ALTER TABLE survey DROP FOREIGN KEY FK_AD5F9BFC4BD2A4C0');
        $this->addSql('ALTER TABLE survey_post DROP FOREIGN KEY FK_9C636C46B3FE509D');
        $this->addSql('ALTER TABLE survey_comment DROP FOREIGN KEY FK_D05871D7B3FE509D');
        $this->addSql('DROP TABLE statistics');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE friend_request');
        $this->addSql('DROP TABLE laws');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_user');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE report');
        $this->addSql('DROP TABLE report_user');
        $this->addSql('DROP TABLE survey');
        $this->addSql('DROP TABLE survey_post');
        $this->addSql('DROP TABLE survey_comment');
    }
}
