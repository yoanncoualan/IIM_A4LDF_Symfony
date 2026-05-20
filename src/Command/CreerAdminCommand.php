<?php

namespace App\Command;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:creer-admin',
    description: 'Création d\'un compte administrateur',
)]
class CreerAdminCommand extends Command
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher, private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $email = null;
        while (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = $io->ask('Adresse email de l\'administrateur');
        }

        $prenom = null;
        while (!$prenom) {
            $prenom = $io->ask('Prénom de l\'administrateur');
        }

        $nom = null;
        while (!$nom) {
            $nom = $io->ask('Nom de l\'administrateur');
        }

        $pwd = null;
        $pwdConfirm = null;
        while (!$pwd || $pwd !== $pwdConfirm || strlen($pwd) < 6) {
            $pwd = $io->askHidden('Mot de passe de l\'administrateur');
            $pwdConfirm = $io->askHidden('Confirmez le mot de passe');
        }

        $role = $io->choice('Role ?', ['ROLE_ADMIN', 'ROLE_MODERATEUR', 'ROLE_EDITOR'], 'ROLE_ADMIN');

        $pwdEtoiles = str_repeat('*', strlen($pwd));

        $io->table(
            ['Email', 'Prénom', 'Nom', 'Role', 'Mot de passe'],
            [[$email, $prenom, $nom, $role, $pwdEtoiles]]
        );

        $confirmation = $io->confirm('Créer l\'administrateur ?', true);

        if(!$confirmation) {
            $io->warning('Création annulée');
            return Command::SUCCESS;
        }

        $client = new Client();
        $client->setEmail($email)
            ->setPrenom($prenom)
            ->setNom($nom)
            ->setRoles([$role])
            ->setPassword($this->passwordHasher->hashPassword($client, $pwd));

        $this->em->persist($client);
        $this->em->flush();

        $io->success('Administrateur créé avec succès !');

        return Command::SUCCESS;
    }
}
