<?php
 
namespace App\Message;
 
final class GenerateFactureMessage
{
    public function __construct(
        public readonly int $commandeId, // ID, pas l'entité elle-même !
    ) {}
}