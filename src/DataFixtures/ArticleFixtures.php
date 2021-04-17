<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ArticleFixtures extends BaseFixtures implements DependentFixtureInterface
{
    private FilesystemOperator $storage;

    public function __construct(FilesystemOperator $articlesStorage)
    {
        $this->storage = $articlesStorage;
    }

    public function loadData(ObjectManager $manager)
    {
        /** @var User $user */
        $user = $this->getReference(
            \sprintf('%s_%d', User::class, $this->faker->numberBetween(0, 9))
        );

        $this->createMany(Article::class, 10, function (Article $article, $count) {
            /** @var User $user */
            $user = $this->getReference(
                \sprintf('%s_%d', User::class, $this->faker->numberBetween(0, 9))
            );

            $image = new UploadedFile($this->faker->image(), (string) $count);
            $imageName = \sprintf('%s.%s', $count, $image->getExtension());

            $this->storage->write(
                $imageName,
                $image->getContent()
            );

            $article->setTitle($this->faker->realText(50))
                ->setContent($this->faker->realText(1000))
                ->setLink($this->faker->url())
                ->setAuthor($user)
                ->setCoverFilename($imageName);
        }, [$user]);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
