<?php

namespace App\Repository;

use App\Entity\Recipe;
use App\Entity\RecipeList;
use App\Entity\RecipeTag;
use App\Entity\User;
use App\Helper\QueryHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog recipe information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function findLatest(int $page = 1): Pagerfanta
    {
        $query = $this->getEntityManager()
            ->createQuery('
                SELECT p, a, t
                FROM App:Recipe p
                JOIN p.author a
                LEFT JOIN p.recipeTags t
                WHERE p.createdAt <= :now
                AND p.private = 0
                ORDER BY p.createdAt DESC
            ')
            ->setParameter('now', new \DateTime())
        ;

        return $this->createPaginator($query, $page);
    }

    private function createPaginator(Query $query, int $page): Pagerfanta
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
        $paginator->setMaxPerPage(Recipe::NUM_ITEMS);
        $paginator->setCurrentPage($page);

        return $paginator;
    }


    public function getMyRecipes($user)
    {
        $queryBuilder = $this->createQueryBuilder('r');
        $queryBuilder->leftJoin('r.collectors', 'c');
        $userGroup = $queryBuilder->expr()->orX();
        $userGroup->add('r.author = :user');
        $userGroup->add('c.id = :user');
        $queryBuilder->andWhere($userGroup);

        $queryBuilder->setParameter('user', $user);
        $queryBuilder->orderBy('r.createdAt', 'DESC');

        return $queryBuilder->getQuery()->getResult();
    }


    /**
     * @return Recipe[]
     */
    public function findBySearchQuery(string $rawQuery, int $limit = Recipe::NUM_ITEMS, ?User $user = null): array
    {
        $query = $this->sanitizeSearchQuery($rawQuery);
        $searchTerms = $this->extractSearchTerms($query);

        if (0 === count($searchTerms)) {
            return [];
        }

        $queryBuilder = $this->createQueryBuilder('p');
        $queryBuilder->leftJoin('p.recipeTags', 't');

        $fields = $queryBuilder->expr()->orX();
        foreach ($searchTerms as $key => $term) {
            $fields->add('p.title LIKE :t_'.$key)
                ->add('p.summary LIKE :t_'.$key)
                ->add('t.name LIKE :t_'.$key);
        }

        $queryBuilder->andWhere($fields)
            ->setParameter('t_'.$key, '%'.$term.'%');
        $owner = $queryBuilder->expr()->orX();
        $owner->add('p.private = 0');
        if ($user) {
            $owner->add('p.private = 1 and p.author = :user');
            $queryBuilder->setParameter(':user', $user);
        }
        $queryBuilder->andWhere($owner);

        return $queryBuilder
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Removes all non-alphanumeric characters except whitespaces.
     */
    private function sanitizeSearchQuery(string $query): string
    {
        return preg_replace('/[^[:alnum:] ]/', '', trim(preg_replace('/[[:space:]]+/', ' ', $query)));
    }

    /**
     * Splits the search query into terms and removes the ones which are irrelevant.
     */
    private function extractSearchTerms(string $searchQuery): array
    {
        $terms = array_unique(explode(' ', mb_strtolower($searchQuery)));

        return array_filter($terms, function ($term) {
            return 2 <= mb_strlen($term);
        });
    }

    private function applyRecipeTagsFilter(QueryBuilder $queryBuilder, $filter)
    {
        if (!($filter['recipeTags'] ?? null)) {
            return;
        }

        if ($filter['recipeTags']->count() == 0) {
            return;
        }

        $queryBuilder->leftJoin('r.recipeTags', 't');
        $fields = $queryBuilder->expr()->andX();
        $allKeywords = [];
        /**
         * @var int $key
         * @var RecipeTag $term
         */
        foreach ($filter['recipeTags'] ?? [] as $key => $term) {
            $allKeywords[] = $term->getId();
        }
        foreach ($filter['recipeTags'] ?? [] as $key => $term) {
            $fields->add('t.id in (:tags)');
            $queryBuilder->setParameter('tags', $allKeywords);
        }
        $queryBuilder->andWhere($fields);
        $queryBuilder->having('count(distinct t.id) >= '.count($allKeywords));
        $queryBuilder->addGroupBy('r.id');
    }

    private function applyIngredientsFilter(QueryBuilder $queryBuilder, $filter)
    {
        if (!($filter['ingredients'] ?? null)) {
            return;
        }

        if ($filter['ingredients']->count() == 0) {
            return;
        }

        $queryBuilder->leftJoin('r.recipeIngredients', 'ri');
        $queryBuilder->leftJoin('ri.ingredient', 'i');
        $fields = $queryBuilder->expr()->andX();
        $allKeywords = [];
        /**
         * @var int $key
         * @var RecipeTag $term
         */
        foreach ($filter['ingredients'] ?? [] as $key => $term) {
            $allIngredients[] = $term->getId();
        }
        foreach ($filter['ingredients'] ?? [] as $key => $term) {
            $fields->add('i.id in (:ingredients)');
            $queryBuilder->setParameter('ingredients', $allIngredients);
        }
        $queryBuilder->andWhere($fields);
        $queryBuilder->having('count(distinct i.id) >= '.count($allIngredients));
        $queryBuilder->addGroupBy('r.id');
    }


    private function applyIngredientsExcludeFilter(QueryBuilder $queryBuilder, $filter)
    {
        if (!($filter['ingredients_exclude'] ?? null)) {
            return;
        }

        if ($filter['ingredients_exclude']->count() == 0) {
            return;
        }

        $queryBuilder->leftJoin('r.recipeIngredients', 'rie');
        $queryBuilder->leftJoin('rie.ingredient', 'ie');
        $fields = $queryBuilder->expr()->orX();
        $allKeywords = [];
        /**
         * @var int $key
         * @var RecipeTag $term
         */
        foreach ($filter['ingredients_exclude'] ?? [] as $key => $term) {
            $allIngredientsExclude[] = $term->getId();
        }
        foreach ($filter['ingredients_exclude'] ?? [] as $key => $term) {
            $fields->add('ie.id = ANY (:ingredients_exclude)');
            $queryBuilder->setParameter('ingredients_exclude', $allIngredientsExclude);
        }
        $queryBuilder->andWhere($fields);
        //$queryBuilder->having('count(distinct ie.id) = 0');
        $queryBuilder->addGroupBy('r.id');
    }

    private function applyRecipeListsFilter(QueryBuilder $queryBuilder, $filter)
    {
        if (!($filter['authorRecipeLists'] ?? null)) {
            return;
        }

        if ($filter['authorRecipeLists']->count() == 0) {
            return;
        }

        $queryBuilder->leftJoin('r.recipeLists', 'rl');
        $fields = $queryBuilder->expr()->andX();
        $allKeywords = [];
        /**
         * @var int $key
         * @var RecipeList $term
         */
        foreach ($filter['authorRecipeLists'] ?? [] as $key => $term) {
            $allKeywords[] = $term->getName();
        }
        foreach ($filter['authorRecipeLists'] ?? [] as $key => $term) {
            $fields->add('rl.name in (:lists)');
            $queryBuilder->setParameter('lists', $allKeywords);
        }
        $queryBuilder->andWhere($fields);
        $queryBuilder->having('count(distinct rl.id) >= '.count($allKeywords));
        $queryBuilder->addGroupBy('rl.id');
    }

    protected function applySearchTermFilter(QueryBuilder $queryBuilder, $filter)
    {
        $terms = array_unique(explode(' ', mb_strtolower($filter['text'])));
        if (empty($terms) || empty($terms[0])) {
            return;
        }
        $fields = $queryBuilder->expr()->orX();
        foreach ($terms as $key => $term) {
            $fields->add('r.title LIKE :t_'.$key)
                ->add('r.summary LIKE :t_'.$key);

            $queryBuilder->setParameter('t_'.$key, '%'.$term.'%');
        }

        $queryBuilder->andWhere($fields);

    }

    protected function applyRatingFilter(QueryBuilder $queryBuilder, $filter)
    {
        if (!($filter['recipeRating'] ?? null)) {
            return;
        }
        $queryBuilder->leftJoin('r.ratings', 'rr');
        $queryBuilder->having('sum(rr.rating) / count(rr.id)>= '.$filter['recipeRating']);
        $queryBuilder->addGroupBy('r.id');
    }

    private function applyRecipeFilters(QueryBuilder $queryBuilder, $filter)
    {
        QueryHelper::andWhereFromFilter($queryBuilder, $filter, 'private', 'r.author');
        $this->applyRecipeTagsFilter($queryBuilder, $filter);
        $this->applyRecipeListsFilter($queryBuilder, $filter);
        $this->applySearchTermFilter($queryBuilder, $filter);
        $this->applyRatingFilter($queryBuilder, $filter);
        $this->applyIngredientsFilter($queryBuilder, $filter);
        //$this->applyIngredientsExcludeFilter($queryBuilder, $filter);
    }

    public function filterRecipes($page, $filter = [], ?User $user = null)
    {
        $queryBuilder = $this->createQueryBuilder('r');
        $this->applyRecipeFilters($queryBuilder, $filter);

        //dump($queryBuilder->getQuery());die;

        return $this->createPaginator($queryBuilder->getQuery(), $page);
    }

    public function getAllForFilter($filter = [], ?User $user = null)
    {
        $queryBuilder = $this->createQueryBuilder('r');
        $this->applyRecipeFilters($queryBuilder, $filter);

        return $queryBuilder->getQuery()->getResult();
    }
}
