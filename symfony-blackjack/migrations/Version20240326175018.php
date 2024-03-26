<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240326175018 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__player_round AS SELECT id, user_id, round_id, creation_date, last_update_date, current_cards, wager FROM player_round');
        $this->addSql('DROP TABLE player_round');
        $this->addSql('CREATE TABLE player_round (id BLOB NOT NULL --(DC2Type:uuid)
        , user_id BLOB DEFAULT NULL --(DC2Type:uuid)
        , round_id BLOB NOT NULL --(DC2Type:uuid)
        , creation_date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , last_update_date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , current_cards CLOB NOT NULL --(DC2Type:json)
        , wager INTEGER NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_7A9C9172A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_7A9C9172A6005CA0 FOREIGN KEY (round_id) REFERENCES round (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO player_round (id, user_id, round_id, creation_date, last_update_date, current_cards, wager) SELECT id, user_id, round_id, creation_date, last_update_date, current_cards, wager FROM __temp__player_round');
        $this->addSql('DROP TABLE __temp__player_round');
        $this->addSql('CREATE INDEX IDX_7A9C9172A6005CA0 ON player_round (round_id)');
        $this->addSql('CREATE INDEX IDX_7A9C9172A76ED395 ON player_round (user_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__round AS SELECT id, game_id, creation_date, last_update_date, cards_left, status FROM round');
        $this->addSql('DROP TABLE round');
        $this->addSql('CREATE TABLE round (id BLOB NOT NULL --(DC2Type:uuid)
        , game_id BLOB DEFAULT NULL --(DC2Type:uuid)
        , creation_date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , last_update_date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , cards_left CLOB NOT NULL --(DC2Type:json)
        , status VARCHAR(255) NOT NULL, dealer_cards CLOB NOT NULL --(DC2Type:json)
        , PRIMARY KEY(id), CONSTRAINT FK_C5EEEA34E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO round (id, game_id, creation_date, last_update_date, cards_left, status) SELECT id, game_id, creation_date, last_update_date, cards_left, status FROM __temp__round');
        $this->addSql('DROP TABLE __temp__round');
        $this->addSql('CREATE INDEX IDX_C5EEEA34E48FD905 ON round (game_id)');
        $this->addSql('ALTER TABLE user ADD COLUMN wallet INTEGER NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__player_round AS SELECT id, user_id, round_id, creation_date, last_update_date, current_cards, wager FROM player_round');
        $this->addSql('DROP TABLE player_round');
        $this->addSql('CREATE TABLE player_round (id BLOB NOT NULL --(DC2Type:uuid)
        , user_id BLOB NOT NULL --(DC2Type:uuid)
        , round_id BLOB NOT NULL --(DC2Type:uuid)
        , creation_date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , last_update_date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , current_cards CLOB NOT NULL --(DC2Type:json)
        , wager INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_7A9C9172A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_7A9C9172A6005CA0 FOREIGN KEY (round_id) REFERENCES round (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO player_round (id, user_id, round_id, creation_date, last_update_date, current_cards, wager) SELECT id, user_id, round_id, creation_date, last_update_date, current_cards, wager FROM __temp__player_round');
        $this->addSql('DROP TABLE __temp__player_round');
        $this->addSql('CREATE INDEX IDX_7A9C9172A76ED395 ON player_round (user_id)');
        $this->addSql('CREATE INDEX IDX_7A9C9172A6005CA0 ON player_round (round_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__round AS SELECT id, game_id, creation_date, last_update_date, cards_left, status FROM round');
        $this->addSql('DROP TABLE round');
        $this->addSql('CREATE TABLE round (id BLOB NOT NULL --(DC2Type:uuid)
        , game_id BLOB NOT NULL --(DC2Type:uuid)
        , creation_date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , last_update_date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , cards_left CLOB NOT NULL --(DC2Type:json)
        , status VARCHAR(255) NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_C5EEEA34E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO round (id, game_id, creation_date, last_update_date, cards_left, status) SELECT id, game_id, creation_date, last_update_date, cards_left, status FROM __temp__round');
        $this->addSql('DROP TABLE __temp__round');
        $this->addSql('CREATE INDEX IDX_C5EEEA34E48FD905 ON round (game_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, email, roles, password, username, creation_date, last_update_date FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id BLOB NOT NULL --(DC2Type:uuid)
        , email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, creation_date DATETIME NOT NULL, last_update_date DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO user (id, email, roles, password, username, creation_date, last_update_date) SELECT id, email, roles, password, username, creation_date, last_update_date FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }
}
