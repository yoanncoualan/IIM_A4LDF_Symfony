<?php
 
namespace App\Message;
 
final class SendEmailMessage
{
    public function __construct(
        public readonly string $destinataire,
        public readonly string $sujet,
        public readonly string $contenuHtml,
        public readonly ?string $fichierJoint = null,
    ) {}
}