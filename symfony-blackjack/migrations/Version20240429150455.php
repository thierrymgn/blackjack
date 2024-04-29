<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240429150455 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE player_round ADD COLUMN gains INTEGER DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__player_round AS SELECT id, user_id, round_id, creation_date, last_update_date, current_cards, wager, status FROM player_round');
        $this->addSql('DROP TABLE player_round');
        $this->addSql('CREATE TABLE player_round (id BLOB NOT NULL --(DC2Type:uuid)
        , user_id BLOB DEFAULT NULL --(DC2Type:uuid)
        , round_id BLOB NOT NULL --(DC2Type:uuid)
        , creation_date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , last_update_date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , current_cards CLOB NOT NULL --(DC2Type:json)
        , wager INTEGER NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_7A9C9172A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_7A9C9172A6005CA0 FOREIGN KEY (round_id) REFERENCES round (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO player_round (id, user_id, round_id, creation_date, last_update_date, current_cards, wager, status) SELECT id, user_id, round_id, creation_date, last_update_date, current_cards, wager, status FROM __temp__player_round');
        $this->addSql('DROP TABLE __temp__player_round');
        $this->addSql('CREATE INDEX IDX_7A9C9172A76ED395 ON player_round (user_id)');
        $this->addSql('CREATE INDEX IDX_7A9C9172A6005CA0 ON player_round (round_id)');
    }
}
