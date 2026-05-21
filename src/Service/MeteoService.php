<?php 

namespace App\Service;

use Psr\Log\LoggerInterface;

class MeteoService
{
    public function __construct(private LoggerInterface $logger)
    {}

    public function getPrevisions(string $ville): array
    {
        $this->logger->info("API : ". $ville);

        $cities = [
            'Paris' => ['temperature' => 15, 'condition' => 'Ensoleillé'], 
            'Lyon' => ['temperature' => 12, 'condition' => 'Nuageux'],
            'Marseille' => ['temperature' => 18, 'condition' => 'Soleil']
        ];

        return $cities[$ville] ?? [];
    }

    public function invalidateCache(string $ville): void
    {}
}