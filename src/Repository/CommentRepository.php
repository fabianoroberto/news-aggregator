<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Comment;
use App\Repository\Traits\HateoasRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository implements CommentRepositoryInterface
{
    use HateoasRepositoryTrait;

    /**
     * @var string
     */
    protected $alias = 'c';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function store(Comment $comment)
    {
        $this->_em->persist($comment);
        $this->_em->flush();
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Comment $comment)
    {
        $this->_em->remove($comment);
        $this->_em->flush();
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
                    case 'article':
                        $qb
                            ->join("{$this->alias}.{$filterName}", 'a')
                            ->andWhere('a.id = :article_id')
                            ->setParameter('article_id', $filterValue, 'uuid');

                        break;

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
}
