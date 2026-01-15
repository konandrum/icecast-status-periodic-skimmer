<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260115160712 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial tables creation';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE audio_stream_item (id UUID NOT NULL, source VARCHAR(64) NOT NULL, server_name VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, listener_counter INT DEFAULT NULL, observed_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX search_idx ON audio_stream_item (source, observed_at)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN audio_stream_item.id IS '(DC2Type:uuid)'
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE audio_stream_item
        SQL);
    }
}
