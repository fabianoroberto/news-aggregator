<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Request\CommentCreateRequest;
use App\Dto\Request\CommentUpdateRequest;
use App\Entity\Article;
use App\Entity\Comment;
use App\Repository\CommentRepositoryInterface;
use Doctrine\ORM\ORMException;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CommentService
{
    private FilesystemOperator $storage;

    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private LoggerInterface $logger,
        FilesystemOperator $commentsStorage,
    ) {
        $this->storage = $commentsStorage;
    }

    public function getPhoto(Comment $comment, $toBase64 = false): string
    {
        $content = $this->storage->read($comment->getPhotoFilename());

        if ($toBase64) {
            $mimeType = $this->storage->mimeType($comment->getPhotoFilename());

            return \sprintf('data: %s;base64,%s', $mimeType, \base64_encode($content));
        }

        return $content;
    }

    public function store(CommentCreateRequest $request, Article $article): Comment
    {
        $this->logger->info('Store comment');

        $comment = (new Comment($article))
            ->setText($request->getText())
            ->setAuthor($request->getAuthor())
            ->setEmail($request->getEmail());

        $this->commentRepository->store($comment);

        return $comment;
    }

    public function update(CommentUpdateRequest $request, Comment $comment): Comment
    {
        $this->logger->info('Update comment');

        $comment->setText($request->getText())
            ->setAuthor($request->getAuthor())
            ->setEmail($request->getEmail());

        $this->commentRepository->store($comment);

        return $comment;
    }

    public function saveImage(Comment $comment, UploadedFile $image): Comment
    {
        $this->logger->info('Update comment');

        $imageName = \sprintf('%s.%s', $comment->getUuid(), $image->guessExtension());

        $this->storage->write(
            $imageName,
            $image->getContent()
        );

        $comment->setPhotoFilename($imageName);

        $this->commentRepository->store($comment);

        return $comment;
    }

    public function delete(Comment $comment): bool
    {
        $this->logger->info('Delete comment');

        try {
            if ($comment->getPhotoFilename()) {
                $this->storage->delete($comment->getPhotoFilename());
            }

            $this->commentRepository->delete($comment);

            return true;
        } catch (FilesystemException $e) {
            $this->logger->error($e->getMessage());

            return false;
        } catch (ORMException $e) {
            $this->logger->error($e->getMessage());

            return false;
        }
    }
}
