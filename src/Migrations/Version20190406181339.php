<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190406181339 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE recipe_user_flags (id INT AUTO_INCREMENT NOT NULL, recipe_id INT NOT NULL, author_id INT NOT NULL, want_to_cook DATETIME DEFAULT NULL, proposed DATETIME DEFAULT NULL, INDEX IDX_A0B93C8159D8A214 (recipe_id), INDEX IDX_A0B93C81F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipe_alternative (id INT AUTO_INCREMENT NOT NULL, recipe_id INT NOT NULL, text LONGTEXT NOT NULL, INDEX IDX_DA0D139E59D8A214 (recipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipe_rating (id INT AUTO_INCREMENT NOT NULL, recipe_id INT NOT NULL, author_id INT DEFAULT NULL, rating INT NOT NULL, created_at DATETIME NOT NULL, enabled TINYINT(1) DEFAULT \'1\' NOT NULL, INDEX IDX_5597380359D8A214 (recipe_id), INDEX IDX_55973803F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, ingredient_display_preference_id INT DEFAULT NULL, active_recipe_list_id INT DEFAULT NULL, daily_dish_recipe_list_id INT DEFAULT NULL, full_name VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, forgot_password_token VARCHAR(255) DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', alt_text VARCHAR(255) DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, image_size INT DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D6492E631EC9 (ingredient_display_preference_id), INDEX IDX_8D93D649494C80CB (active_recipe_list_id), INDEX IDX_8D93D649DA7C4527 (daily_dish_recipe_list_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ingredient (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, de VARCHAR(255) DEFAULT NULL, en VARCHAR(255) DEFAULT NULL, fr VARCHAR(255) DEFAULT NULL, es VARCHAR(255) DEFAULT NULL, density DOUBLE PRECISION DEFAULT NULL, liquid TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipe_hint (id INT AUTO_INCREMENT NOT NULL, recipe_id INT NOT NULL, text LONGTEXT NOT NULL, INDEX IDX_4BDD598C59D8A214 (recipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipe_link (id INT AUTO_INCREMENT NOT NULL, recipe_id INT NOT NULL, text LONGTEXT DEFAULT NULL, url LONGTEXT NOT NULL, INDEX IDX_49B7C32E59D8A214 (recipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipe_list (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, summary VARCHAR(255) DEFAULT NULL, archived TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_3BD3A2C7F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ingredient_display_preference_override (id INT AUTO_INCREMENT NOT NULL, ingredient_id INT DEFAULT NULL, unit_id INT DEFAULT NULL, display_preference_id INT DEFAULT NULL, author_id INT DEFAULT NULL, INDEX IDX_14C9FED1933FE08C (ingredient_id), INDEX IDX_14C9FED1F8BD700D (unit_id), INDEX IDX_14C9FED1529DF284 (display_preference_id), INDEX IDX_14C9FED1F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipe_cooking (id INT AUTO_INCREMENT NOT NULL, recipe_id INT DEFAULT NULL, author_id INT NOT NULL, cooked_at DATETIME NOT NULL, INDEX IDX_A9FE82A59D8A214 (recipe_id), INDEX IDX_A9FE82AF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image_file (id INT AUTO_INCREMENT NOT NULL, recipe_id INT DEFAULT NULL, unique_id VARCHAR(255) DEFAULT NULL, alt_text VARCHAR(255) DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, image_size INT DEFAULT NULL, updated_at DATETIME NOT NULL, INDEX IDX_7EA5DC8E59D8A214 (recipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ref_unit (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, factor_to_liter DOUBLE PRECISION DEFAULT NULL, factor_to_kg DOUBLE PRECISION DEFAULT NULL, de VARCHAR(255) DEFAULT NULL, en VARCHAR(255) DEFAULT NULL, fr VARCHAR(255) DEFAULT NULL, es VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipe_step (id INT AUTO_INCREMENT NOT NULL, recipe_id INT NOT NULL, text LONGTEXT NOT NULL, duration INT DEFAULT NULL, type INT NOT NULL, INDEX IDX_3CA2A4E359D8A214 (recipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ref_ingredient_display_preference (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ref_unit_name (id INT AUTO_INCREMENT NOT NULL, unit_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, language VARCHAR(255) NOT NULL, INDEX IDX_A1BF9E19F8BD700D (unit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipe_ingredient (id INT AUTO_INCREMENT NOT NULL, unit_id INT DEFAULT NULL, ingredient_id INT DEFAULT NULL, recipe_id INT NOT NULL, text VARCHAR(255) DEFAULT NULL, amount DOUBLE PRECISION DEFAULT NULL, INDEX IDX_22D1FE13F8BD700D (unit_id), INDEX IDX_22D1FE13933FE08C (ingredient_id), INDEX IDX_22D1FE1359D8A214 (recipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, recipe_id INT NOT NULL, author_id INT NOT NULL, content LONGTEXT NOT NULL, published_at DATETIME NOT NULL, INDEX IDX_9474526C59D8A214 (recipe_id), INDEX IDX_9474526CF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipe_tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_72DED3CF5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipe (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, title VARCHAR(255) NOT NULL, language VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, summary LONGTEXT DEFAULT NULL, informations LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, portions INT DEFAULT NULL, working_time INT DEFAULT NULL, waiting_time INT DEFAULT NULL, private TINYINT(1) NOT NULL, INDEX IDX_DA88B137F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipe_recipe_tag (recipe_id INT NOT NULL, recipe_tag_id INT NOT NULL, INDEX IDX_3BA055AC59D8A214 (recipe_id), INDEX IDX_3BA055AC37CC7D30 (recipe_tag_id), PRIMARY KEY(recipe_id, recipe_tag_id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipe_recipelists (recipe_id INT NOT NULL, recipe_list_id INT NOT NULL, INDEX IDX_FEEAFB0559D8A214 (recipe_id), INDEX IDX_FEEAFB05714A18CB (recipe_list_id), PRIMARY KEY(recipe_id, recipe_list_id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipe_user (recipe_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_F2888C9659D8A214 (recipe_id), INDEX IDX_F2888C96A76ED395 (user_id), PRIMARY KEY(recipe_id, user_id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE recipe_user_flags ADD CONSTRAINT FK_A0B93C8159D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE recipe_user_flags ADD CONSTRAINT FK_A0B93C81F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE recipe_alternative ADD CONSTRAINT FK_DA0D139E59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE recipe_rating ADD CONSTRAINT FK_5597380359D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE recipe_rating ADD CONSTRAINT FK_55973803F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6492E631EC9 FOREIGN KEY (ingredient_display_preference_id) REFERENCES ref_ingredient_display_preference (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649494C80CB FOREIGN KEY (active_recipe_list_id) REFERENCES recipe_list (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649DA7C4527 FOREIGN KEY (daily_dish_recipe_list_id) REFERENCES recipe_list (id)');
        $this->addSql('ALTER TABLE recipe_hint ADD CONSTRAINT FK_4BDD598C59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE recipe_link ADD CONSTRAINT FK_49B7C32E59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE recipe_list ADD CONSTRAINT FK_3BD3A2C7F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ingredient_display_preference_override ADD CONSTRAINT FK_14C9FED1933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id)');
        $this->addSql('ALTER TABLE ingredient_display_preference_override ADD CONSTRAINT FK_14C9FED1F8BD700D FOREIGN KEY (unit_id) REFERENCES ref_unit (id)');
        $this->addSql('ALTER TABLE ingredient_display_preference_override ADD CONSTRAINT FK_14C9FED1529DF284 FOREIGN KEY (display_preference_id) REFERENCES ref_ingredient_display_preference (id)');
        $this->addSql('ALTER TABLE ingredient_display_preference_override ADD CONSTRAINT FK_14C9FED1F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE recipe_cooking ADD CONSTRAINT FK_A9FE82A59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE recipe_cooking ADD CONSTRAINT FK_A9FE82AF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE image_file ADD CONSTRAINT FK_7EA5DC8E59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE recipe_step ADD CONSTRAINT FK_3CA2A4E359D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE ref_unit_name ADD CONSTRAINT FK_A1BF9E19F8BD700D FOREIGN KEY (unit_id) REFERENCES ref_unit (id)');
        $this->addSql('ALTER TABLE recipe_ingredient ADD CONSTRAINT FK_22D1FE13F8BD700D FOREIGN KEY (unit_id) REFERENCES ref_unit (id)');
        $this->addSql('ALTER TABLE recipe_ingredient ADD CONSTRAINT FK_22D1FE13933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id)');
        $this->addSql('ALTER TABLE recipe_ingredient ADD CONSTRAINT FK_22D1FE1359D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE recipe ADD CONSTRAINT FK_DA88B137F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE recipe_recipe_tag ADD CONSTRAINT FK_3BA055AC59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE recipe_recipe_tag ADD CONSTRAINT FK_3BA055AC37CC7D30 FOREIGN KEY (recipe_tag_id) REFERENCES recipe_tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE recipe_recipelists ADD CONSTRAINT FK_FEEAFB0559D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE recipe_recipelists ADD CONSTRAINT FK_FEEAFB05714A18CB FOREIGN KEY (recipe_list_id) REFERENCES recipe_list (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE recipe_user ADD CONSTRAINT FK_F2888C9659D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE recipe_user ADD CONSTRAINT FK_F2888C96A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE recipe_user_flags DROP FOREIGN KEY FK_A0B93C81F675F31B');
        $this->addSql('ALTER TABLE recipe_rating DROP FOREIGN KEY FK_55973803F675F31B');
        $this->addSql('ALTER TABLE recipe_list DROP FOREIGN KEY FK_3BD3A2C7F675F31B');
        $this->addSql('ALTER TABLE ingredient_display_preference_override DROP FOREIGN KEY FK_14C9FED1F675F31B');
        $this->addSql('ALTER TABLE recipe_cooking DROP FOREIGN KEY FK_A9FE82AF675F31B');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CF675F31B');
        $this->addSql('ALTER TABLE recipe DROP FOREIGN KEY FK_DA88B137F675F31B');
        $this->addSql('ALTER TABLE recipe_user DROP FOREIGN KEY FK_F2888C96A76ED395');
        $this->addSql('ALTER TABLE ingredient_display_preference_override DROP FOREIGN KEY FK_14C9FED1933FE08C');
        $this->addSql('ALTER TABLE recipe_ingredient DROP FOREIGN KEY FK_22D1FE13933FE08C');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649494C80CB');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649DA7C4527');
        $this->addSql('ALTER TABLE recipe_recipelists DROP FOREIGN KEY FK_FEEAFB05714A18CB');
        $this->addSql('ALTER TABLE ingredient_display_preference_override DROP FOREIGN KEY FK_14C9FED1F8BD700D');
        $this->addSql('ALTER TABLE ref_unit_name DROP FOREIGN KEY FK_A1BF9E19F8BD700D');
        $this->addSql('ALTER TABLE recipe_ingredient DROP FOREIGN KEY FK_22D1FE13F8BD700D');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6492E631EC9');
        $this->addSql('ALTER TABLE ingredient_display_preference_override DROP FOREIGN KEY FK_14C9FED1529DF284');
        $this->addSql('ALTER TABLE recipe_recipe_tag DROP FOREIGN KEY FK_3BA055AC37CC7D30');
        $this->addSql('ALTER TABLE recipe_user_flags DROP FOREIGN KEY FK_A0B93C8159D8A214');
        $this->addSql('ALTER TABLE recipe_alternative DROP FOREIGN KEY FK_DA0D139E59D8A214');
        $this->addSql('ALTER TABLE recipe_rating DROP FOREIGN KEY FK_5597380359D8A214');
        $this->addSql('ALTER TABLE recipe_hint DROP FOREIGN KEY FK_4BDD598C59D8A214');
        $this->addSql('ALTER TABLE recipe_link DROP FOREIGN KEY FK_49B7C32E59D8A214');
        $this->addSql('ALTER TABLE recipe_cooking DROP FOREIGN KEY FK_A9FE82A59D8A214');
        $this->addSql('ALTER TABLE image_file DROP FOREIGN KEY FK_7EA5DC8E59D8A214');
        $this->addSql('ALTER TABLE recipe_step DROP FOREIGN KEY FK_3CA2A4E359D8A214');
        $this->addSql('ALTER TABLE recipe_ingredient DROP FOREIGN KEY FK_22D1FE1359D8A214');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C59D8A214');
        $this->addSql('ALTER TABLE recipe_recipe_tag DROP FOREIGN KEY FK_3BA055AC59D8A214');
        $this->addSql('ALTER TABLE recipe_recipelists DROP FOREIGN KEY FK_FEEAFB0559D8A214');
        $this->addSql('ALTER TABLE recipe_user DROP FOREIGN KEY FK_F2888C9659D8A214');
        $this->addSql('DROP TABLE recipe_user_flags');
        $this->addSql('DROP TABLE recipe_alternative');
        $this->addSql('DROP TABLE recipe_rating');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE ingredient');
        $this->addSql('DROP TABLE recipe_hint');
        $this->addSql('DROP TABLE recipe_link');
        $this->addSql('DROP TABLE recipe_list');
        $this->addSql('DROP TABLE ingredient_display_preference_override');
        $this->addSql('DROP TABLE recipe_cooking');
        $this->addSql('DROP TABLE image_file');
        $this->addSql('DROP TABLE ref_unit');
        $this->addSql('DROP TABLE recipe_step');
        $this->addSql('DROP TABLE ref_ingredient_display_preference');
        $this->addSql('DROP TABLE ref_unit_name');
        $this->addSql('DROP TABLE recipe_ingredient');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE recipe_tag');
        $this->addSql('DROP TABLE recipe');
        $this->addSql('DROP TABLE recipe_recipe_tag');
        $this->addSql('DROP TABLE recipe_recipelists');
        $this->addSql('DROP TABLE recipe_user');
    }
}
