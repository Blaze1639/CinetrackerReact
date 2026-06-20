<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260620120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create actualite table (was never created on some deployments since schema changes were not tracked via migrations)';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf($schema->hasTable('actualite'), 'Table actualite already exists.');

        $this->addSql(<<<'SQL'
            CREATE TABLE actualite (
                id INT AUTO_INCREMENT NOT NULL,
                titre VARCHAR(255) NOT NULL,
                contenu LONGTEXT NOT NULL,
                user_id INT NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX IDX_actualite_user_id (user_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 ENGINE = InnoDB
        SQL);

        $this->addSql('ALTER TABLE actualite ADD CONSTRAINT FK_actualite_user_id FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE actualite');
    }
}
