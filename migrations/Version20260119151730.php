<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260119151730 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_info ADD picture_name VARCHAR(255) DEFAULT NULL, DROP firstname, DROP lastname');
        $this->addSql('ALTER TABLE user_info ADD CONSTRAINT FK_B1087D9EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_mod ADD CONSTRAINT FK_FCE232C3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_security ADD CONSTRAINT FK_251631C1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_site_rank ADD CONSTRAINT FK_B303D8F8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_site_rank ADD CONSTRAINT FK_B303D8F8F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)');
        $this->addSql('ALTER TABLE user_site_rank ADD CONSTRAINT FK_B303D8F8D60322AC FOREIGN KEY (role_id) REFERENCES `api-rank` (id)');
        $this->addSql('ALTER TABLE user_stats CHANGE register_on register_on VARCHAR(255) NOT NULL, CHANGE login_on login_on VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user_stats ADD CONSTRAINT FK_B5859CF2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_info DROP FOREIGN KEY FK_B1087D9EA76ED395');
        $this->addSql('ALTER TABLE user_info ADD firstname VARCHAR(50) DEFAULT NULL, ADD lastname VARCHAR(50) DEFAULT NULL, DROP picture_name');
        $this->addSql('ALTER TABLE user_mod DROP FOREIGN KEY FK_FCE232C3A76ED395');
        $this->addSql('ALTER TABLE user_security DROP FOREIGN KEY FK_251631C1A76ED395');
        $this->addSql('ALTER TABLE user_site_rank DROP FOREIGN KEY FK_B303D8F8A76ED395');
        $this->addSql('ALTER TABLE user_site_rank DROP FOREIGN KEY FK_B303D8F8F6BD1646');
        $this->addSql('ALTER TABLE user_site_rank DROP FOREIGN KEY FK_B303D8F8D60322AC');
        $this->addSql('ALTER TABLE user_stats DROP FOREIGN KEY FK_B5859CF2A76ED395');
        $this->addSql('ALTER TABLE user_stats CHANGE register_on register_on VARCHAR(30) NOT NULL, CHANGE login_on login_on VARCHAR(255) DEFAULT NULL');
    }
}
