<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241122134457 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_answer (user_id INT NOT NULL, answer_id INT NOT NULL, INDEX IDX_BF8F5118A76ED395 (user_id), INDEX IDX_BF8F5118AA334807 (answer_id), PRIMARY KEY(user_id, answer_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_question (user_id INT NOT NULL, question_id INT NOT NULL, INDEX IDX_567AAD4EA76ED395 (user_id), INDEX IDX_567AAD4E1E27F6BF (question_id), PRIMARY KEY(user_id, question_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_answer ADD CONSTRAINT FK_BF8F5118A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_answer ADD CONSTRAINT FK_BF8F5118AA334807 FOREIGN KEY (answer_id) REFERENCES answer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_question ADD CONSTRAINT FK_567AAD4EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_question ADD CONSTRAINT FK_567AAD4E1E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A25F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_DADD4A25F675F31B ON answer (author_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_answer DROP FOREIGN KEY FK_BF8F5118A76ED395');
        $this->addSql('ALTER TABLE user_answer DROP FOREIGN KEY FK_BF8F5118AA334807');
        $this->addSql('ALTER TABLE user_question DROP FOREIGN KEY FK_567AAD4EA76ED395');
        $this->addSql('ALTER TABLE user_question DROP FOREIGN KEY FK_567AAD4E1E27F6BF');
        $this->addSql('DROP TABLE user_answer');
        $this->addSql('DROP TABLE user_question');
        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A25F675F31B');
        $this->addSql('DROP INDEX IDX_DADD4A25F675F31B ON answer');
    }
}
