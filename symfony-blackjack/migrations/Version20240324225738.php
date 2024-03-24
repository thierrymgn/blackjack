<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240324225738 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game (id BLOB NOT NULL --(DC2Type:uuid)
        , creation_date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , last_update_date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , status VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE game_user (game_id BLOB NOT NULL --(DC2Type:uuid)
        , user_id BLOB NOT NULL --(DC2Type:uuid)
        , PRIMARY KEY(game_id, user_id), CONSTRAINT FK_6686BA65E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_6686BA65A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_6686BA65E48FD905 ON game_user (game_id)');
        $this->addSql('CREATE INDEX IDX_6686BA65A76ED395 ON game_user (user_id)');
        $this->addSql('CREATE TABLE player_round (id BLOB NOT NULL --(DC2Type:uuid)
        , user_id BLOB NOT NULL --(DC2Type:uuid)
        , round_id BLOB NOT NULL --(DC2Type:uuid)
        , creation_date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , last_update_date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , current_cards CLOB NOT NULL --(DC2Type:json)
        , wager INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_7A9C9172A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_7A9C9172A6005CA0 FOREIGN KEY (round_id) REFERENCES round (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_7A9C9172A76ED395 ON player_round (user_id)');
        $this->addSql('CREATE INDEX IDX_7A9C9172A6005CA0 ON player_round (round_id)');
        $this->addSql('CREATE TABLE round (id BLOB NOT NULL --(DC2Type:uuid)
        , game_id BLOB NOT NULL --(DC2Type:uuid)
        , creation_date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , last_update_date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , cards_left CLOB NOT NULL --(DC2Type:json)
        , status VARCHAR(255) NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_C5EEEA34E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_C5EEEA34E48FD905 ON round (game_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE game_user');
        $this->addSql('DROP TABLE player_round');
        $this->addSql('DROP TABLE round');
    }
}
