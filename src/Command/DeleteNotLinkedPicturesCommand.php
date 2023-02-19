<?php

namespace App\Command;

use App\Repository\PictureRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:delete-not-linked-pictures',
    description: 'Add a short description for your command',
)]
class DeleteNotLinkedPicturesCommand extends Command
{
    public function __construct(private readonly PictureRepository $pictureRepository)
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

        $qb = $this->pictureRepository->createQueryBuilder('p')
            ->where('p.createdAt > :date')
            ->andWhere('p.advert is null')
            ->setParameter(':date', $date);
        $result = $qb->getQuery()->execute();
        $count = count($result);

        if ($count === 0) {
            $io->success('Aucune image ne correspond.');
            return Command::SUCCESS;
        }
        $progress = new ProgressBar($output, $count);
        $progress->start();
        foreach ($result as $advert) {
            $this->pictureRepository->remove($advert, true);
            $progress->advance();
        }
        if ($count === 1) {
            $io->success("1 image a été supprimé avec succès.");
        } else {
            $io->success("$count images ont été supprimés avec succès.");
        }
        return Command::SUCCESS;
    }
}
