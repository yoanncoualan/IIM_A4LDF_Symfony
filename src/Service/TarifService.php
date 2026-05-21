<?php

use Symfony\Component\DependencyInjection\Attribute\Autowire;

class TarifService
{
    public function __construct(
        #[Autowire('%app.tva_rate%')]
        private readonly string $tva_rate,
    )
    {}

    public function htToTtc(float $ht): float
    {
        return $ht * (1 + $this->tva_rate);
    }

    public function ttcToHt(float $ttc): float
    {
        return $ttc / (1 + $this->tva_rate);
    }

    public function getTauxTva(): float
    {
        return $this->tva_rate;
    }
}