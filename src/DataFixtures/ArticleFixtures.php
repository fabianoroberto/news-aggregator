<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ArticleFixtures extends BaseFixtures implements DependentFixtureInterface
{
    public function loadData(ObjectManager $manager)
    {
        $this->createMany(Article::class, 10, function (Article $article) {
            /** @var User $user */
            $user = $this->getReference(
                \sprintf('%s_%d', User::class, $this->faker->numberBetween(0, 9))
            );

            $article->setTitle($this->faker->realText(50))
                ->setContent($this->faker->realText(1000))
                ->setLink($this->faker->url())
                ->setAuthor($user);
        });

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
