<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180830124013 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE name_list ADD CONSTRAINT FK_22D1FE13EDF03354 FOREIGN KEY (name_list_list_id) REFERENCES name_list_object (id)');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE name_list DROP FOREIGN KEY FK_22D1FE13EDF03354');
    }
}


class Version20130326212938 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema)
    {
        // ... migration content
    }

    public function postUp(Schema $schema)
    {
        $converter = $this->container->get('my_service.convert_data_to');
        // ... convert the data from markdown to html for instance
    }
}
