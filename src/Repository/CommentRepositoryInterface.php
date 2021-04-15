<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Comment;
use App\Repository\Common\PaginatorInterface;
use Doctrine\Persistence\ObjectRepository;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface CommentRepositoryInterface extends PaginatorInterface, ObjectRepository
{
}