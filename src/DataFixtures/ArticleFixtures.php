<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Persistence\ObjectManager;

class ArticleFixtures extends BaseFixtures
{
    public function loadData(ObjectManager $manager)
    {
        $this->createMany(Article::class, 10, function (Article $article) {
            $article->setTitle($this->faker->realText(50))
                ->setDescription($this->faker->realText(1000))
                ->setLink($this->faker->url());
        });

        $manager->flush();
    }
}
