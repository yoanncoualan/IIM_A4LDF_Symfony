<?php

namespace App\Service;
 
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
 
class CachedProduitService extends ProduitService
{
    public function __construct(
        private readonly ProduitService $inner,
        private readonly CacheInterface $cache,
    ) {
        // Ne pas appeler parent::__construct()
    }
 
    public function findPopulaires(int $limit = 10): array
    {
        return $this->cache->get(
            "produits_populaires_$limit",
            function (ItemInterface $item) use ($limit) {
                $item->expiresAfter(3600); // 1 heure
                return $this->inner->findPopulaires($limit);
            }
        );
    }
}