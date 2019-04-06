<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190406204046 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE recipe_ingredient_list (id INT AUTO_INCREMENT NOT NULL, recipe_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_73ED1DE259D8A214 (recipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE recipe_ingredient_list ADD CONSTRAINT FK_73ED1DE259D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE recipe_ingredient DROP FOREIGN KEY FK_22D1FE1359D8A214');
        $this->addSql('DROP INDEX IDX_22D1FE1359D8A214 ON recipe_ingredient');
        $this->addSql('ALTER TABLE recipe_ingredient ADD recipe_ingredient_list_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE recipe_ingredient ADD CONSTRAINT FK_22D1FE13EDF03354 FOREIGN KEY (recipe_ingredient_list_id) REFERENCES recipe_ingredient_list (id)');
        $this->addSql('CREATE INDEX IDX_22D1FE13EDF03354 ON recipe_ingredient (recipe_ingredient_list_id)');
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Migrations\IrreversibleMigrationException
     */
    public function down(Schema $schema) : void
    {
        $this->throwIrreversibleMigrationException();
    }
}
