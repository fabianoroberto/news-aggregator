<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends BaseFixtures
{
    public function __construct(private UserPasswordEncoderInterface $passwordEncoder)
    {
    }

    protected function loadData(ObjectManager $manager)
    {
        $className = User::class;

        $username = 'admin';
        $email = \sprintf('%s@newsaggregator.local', $username);
        $entity = new User($email, ['ROLE_ADMIN'], 'Admin', 'NewsAggregator');

        $entity->setPassword($this->passwordEncoder->encodePassword(
            $entity,
            $username
        ));

        $manager->persist($entity);

        for ($i = 0; $i < 10; $i++) {
            $username = $this->faker->userName();
            $email = \sprintf('%s@newsaggregator.local', $username);

            $entity = new User($email, ['ROLE_USER'], $this->faker->firstName(), $this->faker->lastName());

            $entity->setPassword($this->passwordEncoder->encodePassword(
                $entity,
                $username
            ));

            if ($this->faker->boolean()) {
                $manager->persist($entity);
                $manager->flush();
                $manager->remove($entity);
            }

            $manager->persist($entity);
            $this->addReference($className . '_' . $i, $entity);
        }

        $manager->flush();
    }
}
