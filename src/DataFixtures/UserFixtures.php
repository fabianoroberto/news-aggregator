<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private UserPasswordEncoderInterface $passwordEncoder;

    /**
     * UserFixtures constructor.
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $userData = [
            [
                'email' => 'admin@newsaggregator.local',
                'password' => 'admin',
                'firstName' => 'Admin',
                'lastName' => 'News Aggregator',
                'roles' => ['ROLE_ADMIN'],
                'enabled' => true,
            ],
            [
                'email' => 'user@newsaggregator.local',
                'password' => 'user',
                'firstName' => 'User',
                'lastName' => 'News Aggregator',
                'roles' => ['ROLE_USER'],
                'enabled' => true,
            ],
            [
                'email' => 'user+deleted@newsaggregator.local',
                'password' => 'user',
                'firstName' => 'Deleted User',
                'lastName' => 'News Aggregator',
                'roles' => ['ROLE_USER'],
                'enabled' => false,
            ],
        ];

        foreach ($userData as $data) {
            $user = new User($data['email'], $data['roles']);
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                $data['password']
            ));

            $manager->persist($user);
            $manager->flush();

            if ($data['enabled'] === false) {
                $manager->remove($user);
            }

            $manager->flush();
        }
    }
}
