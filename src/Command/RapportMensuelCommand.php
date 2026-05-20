<?php
 
namespace App\Command;
 
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
 
#[AsCommand(
    name: 'app:rapport-mensuel',
    description: 'Génère le rapport mensuel des ventes',
)]
class RapportMensuelCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument('mois', InputArgument::REQUIRED, 'Mois au format YYYY-MM')
            // Option avec valeur
            ->addOption('format', 'f', InputOption::VALUE_OPTIONAL, 'Format de sortie (csv|json)', 'csv')
            // Option sans valeur (flag)
            ->addOption('email', null, InputOption::VALUE_NONE, 'Envoyer le rapport par email')
        ;
    }
 
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
 
        $mois   = $input->getArgument('mois');
        $format = $input->getOption('format');
        $email  = $input->getOption('email');
 
        $io->title("Génération du rapport - $mois");
 
        // Validation basique
        if (!preg_match('/^\d{4}-\d{2}$/', $mois)) {
            $io->error('Format de mois invalide. Utilisez YYYY-MM (ex: 2024-03)');
            return Command::FAILURE;
        }
 
        $io->info("Format de sortie : $format");
 
        // Simulation de traitement
        $io->progressStart(100);
        for ($i = 0; $i < 100; $i++) {
            usleep(10000);
            $io->progressAdvance();
        }
        $io->progressFinish();
 
        if ($email) {
            $io->note('Envoi par email activé');
        }

        // Titres et sections
        $io->title('Mon titre principal');
        $io->section('Étape 1');
        
        // Messages
        $io->info('Information neutre');
        $io->success('Opération réussie');
        $io->warning('Attention !');
        $io->error('Quelque chose a mal tourné');
        $io->note('Note contextuelle');
        
        // Tableaux
        $io->table(
            ['ID', 'Nom', 'Montant'],
            [
                [1, 'Alice', '1 200 €'],
                [2, 'Bob', '850 €'],
            ]
        );
        
        // Liste
        $io->listing(['Étape 1 ✓', 'Étape 2 ✓', 'Étape 3 en cours']);
        
        // Question interactive
        $nom = $io->ask('Quel est votre nom ?');
        $pwd = $io->askHidden('Quel est votre mot de passe ?');
        $confirmation = $io->confirm('Supprimer les anciennes données ?', false);
        $choix = $io->choice('Format ?', ['csv', 'json', 'xml'], 'csv');
 
        $io->success("Rapport $mois généré avec succès !");
 
        return Command::SUCCESS;
    }
}