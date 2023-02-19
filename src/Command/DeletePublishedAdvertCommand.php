<?php

namespace App\Command;

use App\Repository\AdvertRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:delete-published-advert',
    description: 'Add a short description for your command',
)]
class DeletePublishedAdvertCommand extends Command
{
    public function __construct(private readonly AdvertRepository $advertRepository)
    {
        parent::__construct();

    }

    protected function configure(): void
    {
        $this
            ->addArgument('days', InputArgument::REQUIRED, 'dayspan')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $days = intval($input->getArgument('days'));
        if (!$days) {
            $io->error('days argument required');
            return Command::INVALID;
        }
        $date = (New \DateTime())
            ->sub(New \DateInterval("P${days}D"));
        $qb = $this->advertRepository->createQueryBuilder('a')
            ->where('a.publishedAt > :date')
            ->setParameter(':date', $date);

        return Command::SUCCESS;
    }
}
