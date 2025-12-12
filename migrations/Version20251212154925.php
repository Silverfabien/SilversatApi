<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251212154925 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `api-rank` (id INT AUTO_INCREMENT NOT NULL, rolename VARCHAR(20) NOT NULL, role VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE site (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(30) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(20) NOT NULL, is_verify TINYINT NOT NULL, is_verify_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_info (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(50) DEFAULT NULL, lastname VARCHAR(50) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, ip VARCHAR(50) NOT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_B1087D9EA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_mod (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(20) NOT NULL, status_at DATETIME DEFAULT NULL, status_expiration_at DATETIME DEFAULT NULL, status_reason VARCHAR(255) DEFAULT NULL, account_deleted TINYINT NOT NULL, account_deleted_at DATETIME DEFAULT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_FCE232C3A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_security (id INT AUTO_INCREMENT NOT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, confirmation_token_expiration_at DATETIME DEFAULT NULL, reset_password_token VARCHAR(255) DEFAULT NULL, reset_password_token_expiration_at DATETIME DEFAULT NULL, iv VARCHAR(255) NOT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_251631C1A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_site_rank (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, site_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_B303D8F8A76ED395 (user_id), INDEX IDX_B303D8F8F6BD1646 (site_id), INDEX IDX_B303D8F8D60322AC (role_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_stats (id INT AUTO_INCREMENT NOT NULL, last_page_visited VARCHAR(255) NOT NULL, last_page_visited_at DATETIME NOT NULL, number_page_visited INT NOT NULL, last_login_at DATETIME DEFAULT NULL, last_password_changed_at DATETIME DEFAULT NULL, login_attempts INT NOT NULL, number_of_blocked INT NOT NULL, number_of_banned INT NOT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_B5859CF2A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE user_info ADD CONSTRAINT FK_B1087D9EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_mod ADD CONSTRAINT FK_FCE232C3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_security ADD CONSTRAINT FK_251631C1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_site_rank ADD CONSTRAINT FK_B303D8F8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_site_rank ADD CONSTRAINT FK_B303D8F8F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)');
        $this->addSql('ALTER TABLE user_site_rank ADD CONSTRAINT FK_B303D8F8D60322AC FOREIGN KEY (role_id) REFERENCES `api-rank` (id)');
        $this->addSql('ALTER TABLE user_stats ADD CONSTRAINT FK_B5859CF2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_info DROP FOREIGN KEY FK_B1087D9EA76ED395');
        $this->addSql('ALTER TABLE user_mod DROP FOREIGN KEY FK_FCE232C3A76ED395');
        $this->addSql('ALTER TABLE user_security DROP FOREIGN KEY FK_251631C1A76ED395');
        $this->addSql('ALTER TABLE user_site_rank DROP FOREIGN KEY FK_B303D8F8A76ED395');
        $this->addSql('ALTER TABLE user_site_rank DROP FOREIGN KEY FK_B303D8F8F6BD1646');
        $this->addSql('ALTER TABLE user_site_rank DROP FOREIGN KEY FK_B303D8F8D60322AC');
        $this->addSql('ALTER TABLE user_stats DROP FOREIGN KEY FK_B5859CF2A76ED395');
        $this->addSql('DROP TABLE `api-rank`');
        $this->addSql('DROP TABLE site');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_info');
        $this->addSql('DROP TABLE user_mod');
        $this->addSql('DROP TABLE user_security');
        $this->addSql('DROP TABLE user_site_rank');
        $this->addSql('DROP TABLE user_stats');
    }
}
