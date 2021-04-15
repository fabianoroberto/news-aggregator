<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Comment;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CommentFixtures extends BaseFixtures implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            ArticleFixtures::class,
        ];
    }

    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(Comment::class, 50, function (Comment $comment) {
            /** @var Article $article */
            $article = $this->getReference(
                \sprintf('%s_%d', Article::class, $this->faker->numberBetween(0, 9))
            );

            $comment->setText($this->faker->realText(300))
                ->setEmail($this->faker->email())
                ->setAuthor($this->faker->name())
                ->setArticle($article);
        });

        $manager->flush();
    }
}
