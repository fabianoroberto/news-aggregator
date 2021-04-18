<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Article;
use App\Repository\Common\PaginatorInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ObjectRepository;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface ArticleRepositoryInterface extends PaginatorInterface, ObjectRepository
{
    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function store(Article $article);

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Article $article);
}
