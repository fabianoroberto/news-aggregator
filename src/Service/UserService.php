<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Request\UserSetPasswordRequest;
use App\Dto\Request\UserUpdatePasswordRequest;
use App\Dto\Request\UserUpdateRequest;
use App\Entity\User;
use App\Exception\AppException;
use App\Exception\InvalidCredentialsException;
use App\Repository\UserRepositoryInterface;
use DateTime;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class UserService
{
    private const PASSWORD_TTL = 3600;

    private FilesystemOperator $storage;

    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserPasswordEncoderInterface $passwordEncoder,
        private TokenGeneratorInterface $tokenGenerator,
        private MailerInterface $mailer,
        private LoggerInterface $logger,
        private $passwordResetUrl,
        FilesystemOperator $articlesStorage,
    ) {
        $this->storage = $articlesStorage;
    }

    public function getPhoto(User $user, $toBase64 = false): string
    {
        $content = $this->storage->read($user->getPhotoFilename());

        if ($toBase64) {
            $mimeType = $this->storage->mimeType($user->getPhotoFilename());

            return \sprintf('data: %s;base64,%s', $mimeType, \base64_encode($content));
        }

        return $content;
    }

    public function store(UserUpdateRequest $request): User
    {
        $this->logger->info('Store user');

        $user = new User(
            $request->getEmail(),
            [$request->getRole()],
            $request->getFirstName(),
            $request->getLastName()
        );

        $this->userRepository->store($user);

        return $user;
    }

    public function update(UserUpdateRequest $request, User $user): User
    {
        $this->logger->info('Update user');

        $this->userRepository->store($user);

        return $user;
    }

    /**
     * @throws InvalidCredentialsException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function updatePassword(User $user, UserUpdatePasswordRequest $updatePasswordDto): bool
    {
        if (!$this->passwordEncoder->isPasswordValid($user, $updatePasswordDto->getOldPassword())) {
            throw new InvalidCredentialsException('Old password is not valid!');
        }

        $encodedPassword = $this->passwordEncoder->encodePassword($user, $updatePasswordDto->getNewPassword());
        $user->setPassword($encodedPassword);
        $this->userRepository->store($user);

        return true;
    }

    public function saveImage(User $user, UploadedFile $image): User
    {
        $this->logger->info('Update user');

        $imageName = \sprintf('%s.%s', $user->getUuid(), $image->guessExtension());

        $this->storage->write(
            $imageName,
            $image->getContent()
        );

        $user->setPhotoFilename($imageName);

        $this->userRepository->store($user);

        return $user;
    }

    public function delete(User $user): bool
    {
        $this->logger->info('Delete user');

        try {
            if ($user->getPhotoFilename()) {
                $this->storage->delete($user->getPhotoFilename());
            }

            $this->userRepository->delete($user);

            return true;
        } catch (FilesystemException $e) {
            $this->logger->error($e->getMessage());

            return false;
        } catch (ORMException $e) {
            $this->logger->error($e->getMessage());

            return false;
        }
    }

    /**
     * @throws AppException
     */
    public function handleSetPasswordRequest(UserSetPasswordRequest $request): bool
    {
        $user = $this->userRepository->findOneBy(['confirmationToken' => $request->getToken()]);

        if (!$user->isPasswordRequestNonExpired(self::PASSWORD_TTL)) {
            throw new AppException('This token was used or expired!');
        }

        $user->setConfirmationToken(null);
        $user->setPasswordRequestedAt(null);

        $encodedPassword = $this->passwordEncoder->encodePassword($user, $request->getNewPassword());
        $user->setPassword($encodedPassword);
        $this->userRepository->store($user);

        return true;
    }

    /**
     * @throws AppException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function handlePasswordResetRequest(string $email): bool
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if ($user === null) {
            throw new AppException('No account found with this username!');
        }

        if ($user->isPasswordRequestNonExpired(self::PASSWORD_TTL)) {
            throw new AppException('There is a request not still expired!');
        }

        $user->setConfirmationToken($this->tokenGenerator->generateToken());
        $user->setPasswordRequestedAt(new DateTime());

        $this->userRepository->store($user);

        $email = (new TemplatedEmail())
            ->to($user->getEmail())
            ->subject('NewsAggregator | Reset Password')
            ->htmlTemplate('emails/password-reset.html.twig')
            ->context([
                'user' => $user,
                'passwordResetUrl' => \sprintf('%s?token=%s', $this->passwordResetUrl, $user->getConfirmationToken()),
            ]);

        $this->mailer->send($email);

        return true;
    }
}
