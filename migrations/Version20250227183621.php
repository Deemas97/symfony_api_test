<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250227183621 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Products and Coupons data tables';
    }

    public function up(Schema $schema): void
    {
        // Define unsigned types
        $this->addSql('CREATE DOMAIN u_int     AS INTEGER CHECK (VALUE >= 0)');
        $this->addSql('CREATE DOMAIN u_bigint  AS BIGINT CHECK (VALUE >= 0)');
        $this->addSql('CREATE DOMAIN u_numeric AS NUMERIC(10, 2) CHECK (VALUE >= 0)');
        ////

        // Build data tables
        $this->addSql('CREATE SEQUENCE coupon_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE product_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE coupon (id u_bigint NOT NULL, code VARCHAR(20) NOT NULL, type VARCHAR(15) NOT NULL, value u_numeric NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64BF3F0277153098 ON coupon (code)');
        $this->addSql('CREATE TABLE product (id u_bigint NOT NULL, name VARCHAR(255) NOT NULL, price u_int NOT NULL, PRIMARY KEY(id))');
        ////
    }

    public function down(Schema $schema): void
    {
        // Teardown them all
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE coupon_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE product_id_seq CASCADE');
        $this->addSql('DROP TABLE coupon');
        $this->addSql('DROP TABLE product');

        $this->addSql('DROP DOMAIN u_int');
        $this->addSql('DROP DOMAIN u_bigint');
        $this->addSql('DROP DOMAIN u_numeric');
        ////
    }
}
