<?php

namespace App\Command;

use App\Entity\Produit;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-produits',
    description: 'Import de produits via CSV',
)]
class ImportProduitsCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('fichier', InputArgument::REQUIRED, 'Chemin absolu du fichier CSV à importer')
            ->addOption('format', 'f', InputOption::VALUE_OPTIONAL, 'Format de sortie (table|json)', 'table')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $fichier = $input->getArgument('fichier');
        $format = $input->getOption('format');

        $row = 0;
        $produits_crees = 0;
        $produits_mis_a_jour = 0;
        if (($handle = fopen($fichier, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $row++;
                if ($row == 1) {
                    continue;
                } else {
                    $data = explode(';', $data[0]);
        
                    // Vérifie les données obligatoires (reference, nom, prix, stock)
                    if(empty($data[0]) || empty($data[1]) || empty($data[3]) || empty($data[4])) {
                        $io->warning("Ligne $row : Données manquantes, le produit a été ignoré.");
                        continue;
                    }

                    $produit = $this->em->getRepository(Produit::class)->findOneBy(['reference' => $data[0]]);
                    if (!$produit) {
                        $produit = new Produit();
                        $produits_crees++;
                        $io->info("Ligne $row : Produit créé (reference: {$data[0]})");
                        $produit->setReference($data[0]);
                    } else {
                        $produits_mis_a_jour++;
                        $io->info("Ligne $row : Produit mis à jour (reference: {$data[0]})");
                    }

                    $produit->setNom($data[1])
                        ->setDescription($data[2] ?? null)
                        ->setPrix($data[3])
                        ->setStock((int) $data[4]);

                    if(!empty($data[5])) {
                        $produit->setActif($data[5]);
                    }

                    $this->em->persist($produit);

                }
            }
            fclose($handle);

            if($produits_crees > 0 || $produits_mis_a_jour > 0) {
                $this->em->flush();
            }
        }

        if($format === 'json') {
            $resultat = [
                'produits_crees' => $produits_crees,
                'produits_mis_a_jour' => $produits_mis_a_jour,
            ];
            $io->note(json_encode($resultat, JSON_PRETTY_PRINT));
        } else {
            $io->table(
                ['Statut', 'Nombre'],
                [
                    ['Produits créés', $produits_crees],
                    ['Produits mis à jour', $produits_mis_a_jour],
                ]
            );
        }

        $io->success('Importation terminée');

        return Command::SUCCESS;
    }
}
