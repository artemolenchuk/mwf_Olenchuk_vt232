<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261021095014 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cars (id INT AUTO_INCREMENT NOT NULL, brand VARCHAR(255) NOT NULL, model VARCHAR(255) NOT NULL, license_plate VARCHAR(255) NOT NULL, daily_rate NUMERIC(8, 2) NOT NULL, is_available TINYINT NOT NULL, category_id INT NOT NULL, UNIQUE INDEX UNIQ_95C71D14F5AA79D0 (license_plate), INDEX IDX_95C71D1412469DE2 (category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE clients (id INT AUTO_INCREMENT NOT NULL, full_name VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, driver_license VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_C82E7492979079 (driver_license), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE payments (id INT AUTO_INCREMENT NOT NULL, amount NUMERIC(8, 2) NOT NULL, payment_date DATETIME NOT NULL, payment_method VARCHAR(50) NOT NULL, rental_id INT NOT NULL, INDEX IDX_65D29B32A7CF2329 (rental_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE rentals (id INT AUTO_INCREMENT NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, total_price NUMERIC(8, 2) NOT NULL, status VARCHAR(50) NOT NULL, car_id INT NOT NULL, client_id INT NOT NULL, INDEX IDX_35ACDB48C3C6F69F (car_id), INDEX IDX_35ACDB4819EB6921 (client_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE cars ADD CONSTRAINT FK_95C71D1412469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE payments ADD CONSTRAINT FK_65D29B32A7CF2329 FOREIGN KEY (rental_id) REFERENCES rentals (id)');
        $this->addSql('ALTER TABLE rentals ADD CONSTRAINT FK_35ACDB48C3C6F69F FOREIGN KEY (car_id) REFERENCES cars (id)');
        $this->addSql('ALTER TABLE rentals ADD CONSTRAINT FK_35ACDB4819EB6921 FOREIGN KEY (client_id) REFERENCES clients (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cars DROP FOREIGN KEY FK_95C71D1412469DE2');
        $this->addSql('ALTER TABLE payments DROP FOREIGN KEY FK_65D29B32A7CF2329');
        $this->addSql('ALTER TABLE rentals DROP FOREIGN KEY FK_35ACDB48C3C6F69F');
        $this->addSql('ALTER TABLE rentals DROP FOREIGN KEY FK_35ACDB4819EB6921');
        $this->addSql('DROP TABLE cars');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE clients');
        $this->addSql('DROP TABLE payments');
        $this->addSql('DROP TABLE rentals');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
