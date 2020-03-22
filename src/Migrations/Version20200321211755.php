<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200321211755 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(64) NOT NULL, type VARCHAR(64) DEFAULT NULL, date DATETIME NOT NULL, lieu VARCHAR(64) NOT NULL, text LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_user (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, event_id_id INT NOT NULL, INDEX IDX_92589AE29D86650F (user_id_id), INDEX IDX_92589AE23E5F2F7B (event_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, role_string VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag_event (tag_id INT NOT NULL, event_id INT NOT NULL, INDEX IDX_194213A1BAD26311 (tag_id), INDEX IDX_194213A171F7E88B (event_id), PRIMARY KEY(tag_id, event_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, role_id INT NOT NULL, username VARCHAR(64) NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, avatar VARCHAR(255) NOT NULL, INDEX IDX_8D93D649D60322AC (role_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event_user ADD CONSTRAINT FK_92589AE29D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE event_user ADD CONSTRAINT FK_92589AE23E5F2F7B FOREIGN KEY (event_id_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE tag_event ADD CONSTRAINT FK_194213A1BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_event ADD CONSTRAINT FK_194213A171F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649D60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
        $this->addSql('DROP INDEX IDX_3DC1A459A76ED395 ON shopping_list');
        $this->addSql('ALTER TABLE shopping_list ADD event_id INT NOT NULL, DROP user_id');
        $this->addSql('ALTER TABLE shopping_list ADD CONSTRAINT FK_3DC1A45971F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('CREATE INDEX IDX_3DC1A45971F7E88B ON shopping_list (event_id)');
        $this->addSql('ALTER TABLE shopping_list_ingredient ADD CONSTRAINT FK_1AD090A5933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id)');
        $this->addSql('ALTER TABLE shopping_list_ingredient ADD CONSTRAINT FK_1AD090A523245BF9 FOREIGN KEY (shopping_list_id) REFERENCES shopping_list (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE event_user DROP FOREIGN KEY FK_92589AE23E5F2F7B');
        $this->addSql('ALTER TABLE shopping_list DROP FOREIGN KEY FK_3DC1A45971F7E88B');
        $this->addSql('ALTER TABLE tag_event DROP FOREIGN KEY FK_194213A171F7E88B');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649D60322AC');
        $this->addSql('ALTER TABLE tag_event DROP FOREIGN KEY FK_194213A1BAD26311');
        $this->addSql('ALTER TABLE event_user DROP FOREIGN KEY FK_92589AE29D86650F');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE event_user');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE tag_event');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP INDEX IDX_3DC1A45971F7E88B ON shopping_list');
        $this->addSql('ALTER TABLE shopping_list ADD user_id INT DEFAULT NULL, DROP event_id');
        $this->addSql('CREATE INDEX IDX_3DC1A459A76ED395 ON shopping_list (user_id)');
        $this->addSql('ALTER TABLE shopping_list_ingredient DROP FOREIGN KEY FK_1AD090A5933FE08C');
        $this->addSql('ALTER TABLE shopping_list_ingredient DROP FOREIGN KEY FK_1AD090A523245BF9');
    }
}
