<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Comment;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CommentFixtures extends BaseFixtures implements DependentFixtureInterface
{
    private FilesystemOperator $storage;

    public function __construct(FilesystemOperator $commentsStorage)
    {
        $this->storage = $commentsStorage;
    }

    public function getDependencies(): array
    {
        return [
            ArticleFixtures::class,
        ];
    }

    protected function loadData(ObjectManager $manager)
    {
        /** @var Article $article */
        $article = $this->getReference(
            \sprintf('%s_%d', Article::class, $this->faker->numberBetween(0, 9))
        );

        $this->createMany(Comment::class, 50, function (Comment $comment, $count) {
            /** @var Article $article */
            $article = $this->getReference(
                \sprintf('%s_%d', Article::class, $this->faker->numberBetween(0, 9))
            );

            $image = new UploadedFile($this->faker->image(), (string) $count);
            $imageName = \sprintf('%s.%s', $count, $image->getExtension());

            $this->storage->write(
                $imageName,
                $image->getContent()
            );

            $comment->setText($this->faker->realText(300))
                ->setEmail($this->faker->email())
                ->setAuthor($this->faker->name())
                ->setArticle($article)
                ->setPhotoFilename($imageName);
        }, [$article]);

        $manager->flush();
    }
}
