<?php

declare(strict_types=1);

namespace console\migrations\orm;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200717165627 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE "user" (id BIGINT NOT NULL, username VARCHAR(255) NOT NULL, auth_key VARCHAR(32) NOT NULL, password_hash VARCHAR(255) NOT NULL, password_reset_token VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, status SMALLINT DEFAULT 10 NOT NULL, created_at INT NOT NULL, updated_at INT NOT NULL, verification_token VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE SEQUENCE user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d649f85e0677 ON "user" (username)');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d649e7927c74 ON "user" (email)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP SEQUENCE "user_id_seq"');
    }
}
