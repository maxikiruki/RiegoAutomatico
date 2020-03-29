<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200329181541 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE history (id INT AUTO_INCREMENT NOT NULL, sector_id INT DEFAULT NULL, date DATE NOT NULL, start_time TIME NOT NULL, end_time TIME NOT NULL, start_humidity INT NOT NULL, final_humidity INT NOT NULL, total_liters INT NOT NULL, INDEX IDX_27BA704BDE95C867 (sector_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE schedule (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, start_time_morning TIME DEFAULT NULL, end_time_morning TIME DEFAULT NULL, start_time_afternoon TIME DEFAULT NULL, end_time_afternoon TIME DEFAULT NULL, visible TINYINT(1) NOT NULL, monday TINYINT(1) NOT NULL, tuesday TINYINT(1) NOT NULL, wednesday TINYINT(1) NOT NULL, thursday TINYINT(1) NOT NULL, friday TINYINT(1) NOT NULL, saturday TINYINT(1) NOT NULL, sunday TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sector (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, schedule_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, valve VARCHAR(255) DEFAULT NULL, humedity VARCHAR(255) DEFAULT NULL, flowmeter VARCHAR(255) DEFAULT NULL, INDEX IDX_4BA3D9E87E3C61F9 (owner_id), INDEX IDX_4BA3D9E8A40BC2D5 (schedule_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE state (id INT AUTO_INCREMENT NOT NULL, sector_id INT NOT NULL, on_off TINYINT(1) NOT NULL, programmed TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_A393D2FBDE95C867 (sector_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, phone INT DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE history ADD CONSTRAINT FK_27BA704BDE95C867 FOREIGN KEY (sector_id) REFERENCES sector (id)');
        $this->addSql('ALTER TABLE sector ADD CONSTRAINT FK_4BA3D9E87E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE sector ADD CONSTRAINT FK_4BA3D9E8A40BC2D5 FOREIGN KEY (schedule_id) REFERENCES schedule (id)');
        $this->addSql('ALTER TABLE state ADD CONSTRAINT FK_A393D2FBDE95C867 FOREIGN KEY (sector_id) REFERENCES sector (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sector DROP FOREIGN KEY FK_4BA3D9E8A40BC2D5');
        $this->addSql('ALTER TABLE history DROP FOREIGN KEY FK_27BA704BDE95C867');
        $this->addSql('ALTER TABLE state DROP FOREIGN KEY FK_A393D2FBDE95C867');
        $this->addSql('ALTER TABLE sector DROP FOREIGN KEY FK_4BA3D9E87E3C61F9');
        $this->addSql('DROP TABLE history');
        $this->addSql('DROP TABLE schedule');
        $this->addSql('DROP TABLE sector');
        $this->addSql('DROP TABLE state');
        $this->addSql('DROP TABLE user');
    }
}
