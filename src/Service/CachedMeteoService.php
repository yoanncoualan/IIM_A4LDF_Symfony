<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CachedMeteoService extends MeteoService
{
    public function __construct(
        private readonly MeteoService $inner,
        private CacheInterface $cache,
        private LoggerInterface $logger,
        #[Autowire('%app.meteo_cache_ttl%')]
        private int $cacheTtl
    ) {}

    public function getPrevisions(string $ville): array
    {
        $this->logger->info("Cache : ". $ville);

        return $this->cache->get(
            "meteo_$ville",
            function (ItemInterface $item) use ($ville) {
                $item->expiresAfter($this->cacheTtl); // Utilise la TTL configurée
                return $this->inner->getPrevisions($ville);
            }
        );
    }

    public function invalidateCache(string $ville): void
    {
        $this->cache->delete("meteo_$ville");
    }
}