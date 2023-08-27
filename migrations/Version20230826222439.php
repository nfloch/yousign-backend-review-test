<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20230826222439 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add remote ids to database entities';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE actor_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "event_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE repo_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE actor ADD gha_id BIGINT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX actor_gha_id ON actor (gha_id)');
        $this->addSql('ALTER TABLE event ADD gha_id BIGINT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX event_gha_id ON event (gha_id)');
        $this->addSql('ALTER TABLE repo ADD gha_id BIGINT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX repo_gha_id ON repo (gha_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE actor_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "event_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE repo_id_seq CASCADE');
        $this->addSql('DROP INDEX actor_gha_id');
        $this->addSql('ALTER TABLE actor DROP gha_id');
        $this->addSql('DROP INDEX repo_gha_id');
        $this->addSql('ALTER TABLE repo DROP gha_id');
        $this->addSql('DROP INDEX event_gha_id');
        $this->addSql('ALTER TABLE "event" DROP gha_id');
    }
}
