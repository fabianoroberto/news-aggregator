<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use App\Service\ArticleService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportRssNewsCommand extends Command
{
    protected static $defaultName = 'app:import-rss-news';
    protected static $defaultDescription = 'Import news from feed RSS';

    public function __construct(
        private ArticleService $articleService,
        private UserRepositoryInterface $userRepository,
        private LoggerInterface $logger
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('url', InputArgument::OPTIONAL, 'Url of feed RSS');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $url = $input->getArgument('url');

        if ($url && \filter_var($url, \FILTER_VALIDATE_URL)) {
            $infoMessage = \sprintf('You passed an argument: %s', $url);
            $io->note($infoMessage);
            $this->logger->info($infoMessage);

            $user = $this->userRepository->findOneByRole(User::ROLE_ADMIN);
            $this->articleService->import($url, $user);

            return Command::SUCCESS;
        }

        $errorMessage = 'You need pass a valid url';
        $io->error($errorMessage);
        $this->logger->error($errorMessage);

        return Command::FAILURE;
    }
}
