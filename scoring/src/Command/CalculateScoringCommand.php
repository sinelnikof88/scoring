<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Client;
use App\Repository\ClientRepository;
use App\Service\Scoring\ScoringService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * php bin/console app:scoring:calculate.
 */
#[AsCommand(
    name: 'app:scoring:calculate',
    description: 'Пересчитывает скоринг клиентов и сохраняет в БД',
)]
final class CalculateScoringCommand extends Command
{
    public function __construct(
        private readonly ClientRepository $clients,
        private readonly ScoringService $scoring,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'id',
            InputArgument::OPTIONAL,
            'ID клиента. Если не указан — рассчитывается по всем клиентам.',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $id = $input->getArgument('id');

        if (null !== $id) {
            $client = $this->clients->find((int) $id);

            if (null === $client) {
                $io->error(sprintf('Клиент с id=%d не найден.', $id));

                return Command::FAILURE;
            }

            $this->processClient($client, $io);
            $this->em->flush();

            return Command::SUCCESS;
        }

        $all = $this->clients->findAll();

        if ([] === $all) {
            $io->warning('В базе нет клиентов.');

            return Command::SUCCESS;
        }

        foreach ($all as $client) {
            $this->processClient($client, $io);
        }

        $this->em->flush();

        $io->success(sprintf('Скоринг пересчитан для %d клиент(ов).', count($all)));

        return Command::SUCCESS;
    }

    /**
     * Считает скоринг клиента, сохраняет в сущность и выводит детализацию.
     */
    private function processClient(Client $client, SymfonyStyle $io): void
    {
        $result = $this->scoring->calculate($client);
        $client->setScore($result['total']);

        $io->section(sprintf(
            'Клиент #%d: %s %s (%s)',
            $client->getId(),
            $client->getFirstName(),
            $client->getLastName(),
            $client->getEmail(),
        ));

        $rows = [];
        foreach ($result['details'] as $rule => $score) {
            $rows[] = [$rule, sprintf('%+d', $score)];
        }
        $rows[] = ['ИТОГО', (string) $result['total']];

        $io->table(['Правило', 'Баллы'], $rows);
    }
}
