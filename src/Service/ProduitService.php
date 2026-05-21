<?php
// service original
 
namespace App\Service;
 
use App\Repository\ProduitRepository;
 
class ProduitService
{
    public function __construct(
        private readonly ProduitRepository $repository,
    ) {}
 
    public function findPopulaires(int $limit = 10): array
    {
        // Requête coûteuse
        return $this->repository->findOrderedByVentes($limit);
    }
}