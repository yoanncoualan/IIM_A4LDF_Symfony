<?php

namespace App\Controller;

use App\Entity\Client;
use App\Service\FideliteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\DependencyInjection\Attribute\Lazy;

final class ClientController extends AbstractController
{
    #[Route('/client/{id}/score', name: 'client_score')]
    public function score(
        Client $client, 
        #[Lazy]
        FideliteService $fidelite): Response
    {
        if (!$client) {
            throw $this->createNotFoundException('Client non trouvé');
        }

        $score = $fidelite->calculerScore($client);
 
        $niveau = $fidelite->getNiveau($score);
 
        return $this->render('client/score.html.twig', [
            'client' => $client,
            'score'  => $score,
            'niveau' => $niveau,
        ]);
    }
}
