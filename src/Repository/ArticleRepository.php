<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Article;
use App\Repository\Traits\HateoasRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository implements ArticleRepositoryInterface
{
    use HateoasRepositoryTrait;

    /**
     * @var string
     */
    protected $alias = 'a';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function store(Article $article)
    {
        $this->_em->persist($article);
        $this->_em->flush($article);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Article $article)
    {
        $this->_em->remove($article);
        $this->_em->flush($article);
    }
}
