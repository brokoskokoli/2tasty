<?php declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Entity\RecipeIngredientList;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190406223022 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema) : void
    {
        /** @var EntityManagerInterface $em */
        $em = $this->container->get('doctrine.orm.entity_manager');
        $connection = $em->getConnection();
        $query = $connection->prepare('SELECT * FROM recipe_ingredient');
        $query->execute();
        $results = $query->fetchAll(FetchMode::ASSOCIATIVE);

        foreach ($results as $id => $recipe_ingredient) {

            $rilr = $em->getRepository(RecipeIngredientList::class);
            $recipe_ingredient_list = $rilr->findOneBy(['recipe' => $recipe_ingredient['recipe_id']]);

            if (!$recipe_ingredient_list) {
                $recipe_ingredient_list = new RecipeIngredientList();
                $recipe_ingredient_list->setRecipe($em->getReference(Recipe::class, $recipe_ingredient['recipe_id']));
                $recipe_ingredient_list->setTitle('');
                $em->persist($recipe_ingredient_list);
            }

            $query = $connection->prepare('UPDATE recipe_ingredient SET recipe_ingredient_list_id = :list WHERE recipe_id = :id');
            $query->execute([
                ':list' => $recipe_ingredient_list->getId(),
                ':id' => $recipe_ingredient['recipe_id'],
            ]);

        }

        $em->flush();
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
