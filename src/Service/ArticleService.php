<?php


namespace App\Service;

use App\Dto\Request\ArticleCreateRequest;
use App\Dto\Request\ArticleUpdateRequest;
use App\Entity\Article;
use App\Entity\User;
use App\Repository\ArticleRepositoryInterface;
use League\Flysystem\FilesystemOperator;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ArticleService
{
    private FilesystemOperator $storage;

    public function __construct(
        private ArticleRepositoryInterface $articleRepository,
        private LoggerInterface $logger,
        FilesystemOperator $articlesStorage,
    ) {
        $this->storage = $articlesStorage;
    }

    public function getCover(Article $article, $toBase64 = false): string
    {
        $content = $this->storage->read($article->getCoverFilename());

        if ($toBase64) {
            $mimeType = $this->storage->mimeType($article->getCoverFilename());

            return \sprintf('data: %s;base64,%s', $mimeType, \base64_encode($content));
        }

        return $content;
    }

    public function store(ArticleCreateRequest $request, User $author): Article
    {
        $this->logger->info('Article store by Admin');

        $article = (new Article($author))
            ->setTitle($request->getTitle())
            ->setContent($request->getContent())
            ->setLink($request->getLink());

        $this->articleRepository->store($article);

        return $article;
    }

    public function update(ArticleUpdateRequest $request, Article $article): Article
    {
        $article->setTitle($request->getTitle())
            ->setContent($request->getContent())
            ->setLink($request->getLink());

        $this->articleRepository->store($article);

        return $article;
    }

    public function saveImage(Article $article, UploadedFile $image): Article
    {
        $imageName = \sprintf('%s.%s', $article->getUuid(), $image->getExtension());

        $this->storage->write(
            $imageName,
            $image->getContent()
        );

        $article->setCoverFilename($imageName);

        $this->articleRepository->store($article);

        return $article;
    }
}