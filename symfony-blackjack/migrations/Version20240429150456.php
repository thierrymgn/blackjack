<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\User;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240429150456 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $admin = new User();
        $admin->setEmail('admin@gmail.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword('$2y$13$hnp.m9rUf0RGS0rG5pG66Oh.LQVXMLm7WZzBsyX9f71uZR8./qk9a');
        $admin->setUsername('admin');
        $admin->setWallet(1000);
        $admin->setCreationDate(new \DateTimeImmutable());
        $admin->setLastUpdateDate(new \DateTimeImmutable());


        $this->addSql('INSERT INTO user (id, email, roles, password, username, wallet, creation_date, last_update_date) VALUES (:id, :email, :roles, :password, :username, :wallet, :creation_date, :last_update_date)', [
            'id' => 'fbff7b8d-d51c-418c-ae49-c2d09341011a',
            'email' => $admin->getEmail(),
            'roles' => json_encode($admin->getRoles()),
            'password' => $admin->getPassword(),
            'username' => $admin->getUsername(),
            'wallet' => $admin->getWallet(),
            'creation_date' => $admin->getCreationDate()->format('Y-m-d H:i:s'),
            'last_update_date' => $admin->getLastUpdateDate()->format('Y-m-d H:i:s')
        ]);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
