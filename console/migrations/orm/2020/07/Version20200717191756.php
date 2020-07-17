<?php

declare(strict_types=1);

namespace console\migrations\orm;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200717191756 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE auth_rule (name VARCHAR(64) NOT NULL, data BYTEA DEFAULT NULL, created_at INT DEFAULT NULL, updated_at INT DEFAULT NULL, PRIMARY KEY(name))');
        $this->addSql('CREATE TABLE auth_item_child (parent VARCHAR(64) NOT NULL, child VARCHAR(64) NOT NULL, PRIMARY KEY(parent, child))');
        $this->addSql('CREATE INDEX IDX_1611424D3D8E604F ON auth_item_child (parent)');
        $this->addSql('CREATE INDEX IDX_1611424D22B35429 ON auth_item_child (child)');
        $this->addSql('CREATE TABLE auth_item (name VARCHAR(64) NOT NULL, rule_name VARCHAR(64) DEFAULT NULL, type SMALLINT NOT NULL, description TEXT DEFAULT NULL, data BYTEA DEFAULT NULL, created_at INT DEFAULT NULL, updated_at INT DEFAULT NULL, PRIMARY KEY(name))');
        $this->addSql('CREATE INDEX "idx-auth_item-type" ON auth_item (type)');
        $this->addSql('CREATE INDEX IDX_313DC5AADE72171 ON auth_item (rule_name)');
        $this->addSql('CREATE TABLE auth_assignment (item_name VARCHAR(64) NOT NULL, user_id VARCHAR(64) NOT NULL, created_at INT DEFAULT NULL, PRIMARY KEY(item_name, user_id))');
        $this->addSql('CREATE INDEX "idx-auth_assignment-user_id" ON auth_assignment (user_id)');
        $this->addSql('CREATE INDEX IDX_2EC0490E96133AFD ON auth_assignment (item_name)');
        $this->addSql('ALTER TABLE auth_item_child ADD CONSTRAINT auth_item_child_parent_fkey FOREIGN KEY (parent) REFERENCES auth_item (name) ON UPDATE CASCADE ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE auth_item_child ADD CONSTRAINT auth_item_child_child_fkey FOREIGN KEY (child) REFERENCES auth_item (name) ON UPDATE CASCADE ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE auth_item ADD CONSTRAINT auth_item_rule_name_fkey FOREIGN KEY (rule_name) REFERENCES auth_rule (name) ON UPDATE CASCADE ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE auth_assignment ADD CONSTRAINT auth_assignment_item_name_fkey FOREIGN KEY (item_name) REFERENCES auth_item (name) ON UPDATE CASCADE ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE auth_item DROP CONSTRAINT auth_item_rule_name_fkey');
        $this->addSql('ALTER TABLE auth_item_child DROP CONSTRAINT auth_item_child_parent_fkey');
        $this->addSql('ALTER TABLE auth_item_child DROP CONSTRAINT auth_item_child_child_fkey');
        $this->addSql('ALTER TABLE auth_assignment DROP CONSTRAINT auth_assignment_item_name_fkey');
        $this->addSql('DROP TABLE auth_rule');
        $this->addSql('DROP TABLE auth_item_child');
        $this->addSql('DROP TABLE auth_item');
        $this->addSql('DROP TABLE auth_assignment');
    }
}
