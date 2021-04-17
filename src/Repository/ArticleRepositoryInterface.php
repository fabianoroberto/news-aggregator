<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Article;
use App\Repository\Common\PaginatorInterface;
use Doctrine\Persistence\ObjectRepository;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface ArticleRepositoryInterface extends PaginatorInterface, ObjectRepository
{
    public function store(Article $article);
}
