<?php

namespace App\Service;

use App\Entity\Client;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class FideliteService
{
    public function __construct(
        #[Autowire('%app.upload_dir%')]
        private readonly string $uploadDir,
        #[Autowire('%app.max_file_size%')]
        private readonly int $maxFileSize,)
    {
        // Ici on pourrait injecter d'autres services si nécessaire
    }

    public function calculerScore(Client $client): int 
    {
        $score = 0;
 
        // +1 point par commande validée
        $score += $client->getCommandes()->filter(
            fn($c) => $c->getStatut() === 'validee'
        )->count();
 
        // Bonus si client depuis plus de 2 ans
        $anciennete = $client->getCreatedAt()->diff(new \DateTimeImmutable());
        if ($anciennete->y >= 2) {
            $score += 20;
        }
 
        // Bonus si total achats > 1000€
        $totalAchats = array_sum(
            $client->getCommandes()->map(fn($c) => $c->getTotal())->toArray()
        );
        if ($totalAchats > 1000) {
            $score += 50;
        }

        return $score;
    }

    public function getNiveau(int $score): string
    {
         // Attribution du niveau
        $niveau = match(true) {
            $score >= 100 => 'Platine',
            $score >= 50  => 'Or',
            $score >= 20  => 'Argent',
            default       => 'Bronze',
        };

        return $niveau;
    }
}