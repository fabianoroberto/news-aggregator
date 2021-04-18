<?php

declare(strict_types=1);

namespace App\Repository\Traits;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

trait HateoasRepositoryTrait
{
    public function getPaginatorByFilters(
        array $filters,
        array $orderBy = null,
        int $page = 1,
        int $limit = 10
    ): Pagerfanta {
        /** @var EntityManager $em */
        $em = $this->getEntityManager();

        $cm = $em->getClassMetadata($this->_entityName);

        $fieldNames = $cm->getFieldNames();
        $associations = $cm->getAssociationNames();

        //all entity field names plus association fields
        $fields = \array_merge($fieldNames, $associations);

        $qb = $this->createQueryBuilderByFilters($filters, $fields);

        if ($orderBy !== null) {
            foreach ($orderBy as $key => $value) {
                if (\in_array($key, $fields, true)) {
                    $key = "{$this->alias}.{$key}";
                }

                $qb->addOrderBy($key, $value);
            }
        }

        return $this->getPager($qb, $page, $limit);
    }

    protected function createQueryBuilderByFilters(array $filters, array $fields = ['id'], bool $isCount = false): QueryBuilder
    {
        $qb = $this->createQueryBuilder($this->alias);

        $groupBy = [];

        if (\count($filters) > 0) {
            $params = [];
            $andX = [];
            $orX = [];

            foreach ($filters as $filterName => $filterValue) {
                switch ($filterName) {
                    case 'not':
                        foreach ($filterValue as $subFilterName => $subFilterValue) {
                            $subFilterNameParam = 'not_' . $subFilterName;

                            $andX[] = $qb->expr()->notIn("{$this->alias}.{$subFilterName}", ':' . $subFilterNameParam);
                            $params[$subFilterNameParam] = $subFilterValue;
                        }

                        break;

                    case 'like':
                        foreach ($filterValue as $subFilterName => $subFilterValue) {
                            $subFilterNameParam = 'like_' . $subFilterName;

                            $andX[] = $qb->expr()->like("{$this->alias}.{$subFilterName}", ':' . $subFilterNameParam);
                            $params[$subFilterNameParam] = $subFilterValue;
                        }

                        break;

                    case 'lt':
                    case 'lte':
                    case 'gt':
                    case 'gte':
                    case 'eq':
                    case 'neq':
                        foreach ($filterValue as $subFilterName => $subFilterValue) {
                            if (\in_array($subFilterName, $fields, true)) {
                                $subFilterNameParam = "{$filterName}_{$subFilterName}";

                                $andX[] = $qb->expr()->{$filterName}(
                                    "{$this->alias}.{$subFilterName}",
                                    ':' . $subFilterNameParam
                                );

                                $params[$subFilterNameParam] = $subFilterValue;
                            }
                        }

                        break;

                    case 'search': // or statement
                        foreach ($filterValue as $subFilterName => $subFilterValue) {
                            $subFilterNameParam = 'like_' . $subFilterName;

                            $orX[] = $qb->expr()->like("{$this->alias}.{$subFilterName}", ':' . $subFilterNameParam);
                            $params[$subFilterNameParam] = $subFilterValue;
                        }

                        break;

                    case $filterName:
                        if (\in_array($filterName, $fields, true)) {
                            $andX[] = $qb->expr()->in("{$this->alias}.{$filterName}", ':' . $filterName);
                            $params[$filterName] = $filterValue;
                        }

                        break;
                }
            }

            if (\count($orX) > 0) {
                $qb->orWhere('(' . \implode(') OR (', $orX) . ')');
                $qb->setParameters($params);
            }
            if (\count($andX) > 0) {
                $qb->andWhere('(' . \implode(') AND (', $andX) . ')');
                $qb->setParameters($params);
            }
        }

        if ($isCount === false) {
            foreach ($groupBy as $value) {
                $qb->addGroupBy($value);
            }
        }

        return $qb;
    }

    private function getPager(QueryBuilder $qb, int $page = 1, int $limit = 10): Pagerfanta
    {
        $pagerAdapter = new QueryAdapter($qb);

        $pager = new Pagerfanta($pagerAdapter);
        $pager->setCurrentPage($page);
        $pager->setMaxPerPage($limit);

        return $pager;
    }
}
