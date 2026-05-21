<?php

namespace App\Command;

use App\Service\MeteoService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'MeteoCommand',
    description: 'Add a short description for your command',
)]
class MeteoCommand extends Command
{
    public function __construct(private MeteoService $meteoService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('ville', InputArgument::REQUIRED, 'La ville')
            ->addOption('invalidate', null, InputOption::VALUE_NONE, 'Invalider le cache pour cette ville')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $ville = $input->getArgument('ville');
        $invalidate = $input->getOption('invalidate');

        $io->title("Prévisions météo pour $ville");

        if ($invalidate) {
            $this->meteoService->invalidateCache($ville);
            $io->warning("Cache invalidé pour $ville");
            return Command::SUCCESS;
        }

        if (!$ville) {
            $io->error("Veuillez spécifier une ville.");
            return Command::FAILURE;
        }
        
        $meteo = $this->meteoService->getPrevisions($ville);

        $io->table(
            ['Ville', 'Température', 'Condition'],
            [[$ville, $meteo['temperature'] ?? 'N/A', $meteo['condition'] ?? 'N/A']]
        );

        return Command::SUCCESS;
    }
}
