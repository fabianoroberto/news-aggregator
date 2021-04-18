<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Repository\Common\PaginatorInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ObjectRepository;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface UserRepositoryInterface extends PaginatorInterface, ObjectRepository
{
    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function store(User $user);

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(User $user);
}
