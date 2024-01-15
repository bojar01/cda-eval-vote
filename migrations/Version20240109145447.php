<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240109145447 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD session_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649613FECDF ON user (session_id)');
        $this->addSql('ALTER TABLE user_vote ADD vote_id INT NOT NULL, ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_vote ADD CONSTRAINT FK_2091C9AD72DCDAFC FOREIGN KEY (vote_id) REFERENCES vote (id)');
        $this->addSql('ALTER TABLE user_vote ADD CONSTRAINT FK_2091C9ADA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_2091C9AD72DCDAFC ON user_vote (vote_id)');
        $this->addSql('CREATE INDEX IDX_2091C9ADA76ED395 ON user_vote (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649613FECDF');
        $this->addSql('DROP INDEX IDX_8D93D649613FECDF ON user');
        $this->addSql('ALTER TABLE user DROP session_id');
        $this->addSql('ALTER TABLE user_vote DROP FOREIGN KEY FK_2091C9AD72DCDAFC');
        $this->addSql('ALTER TABLE user_vote DROP FOREIGN KEY FK_2091C9ADA76ED395');
        $this->addSql('DROP INDEX IDX_2091C9AD72DCDAFC ON user_vote');
        $this->addSql('DROP INDEX IDX_2091C9ADA76ED395 ON user_vote');
        $this->addSql('ALTER TABLE user_vote DROP vote_id, DROP user_id');
    }
}
