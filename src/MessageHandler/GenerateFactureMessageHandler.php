<?php
 
namespace App\MessageHandler;
 
use App\Message\GenerateFactureMessage;
use App\Message\SendEmailMessage;
use App\Repository\CommandeRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
 
#[AsMessageHandler]
class GenerateFactureMessageHandler
{
    public function __construct(
        private readonly CommandeRepository $commandeRepository,
        private readonly MessageBusInterface $bus,
    ) {}
 
    public function __invoke(GenerateFactureMessage $message): void
    {
        $commande = $this->commandeRepository->find($message->commandeId);
 
        if (!$commande) {
            // La commande a peut-être été supprimée entre le dispatch et le traitement
            return;
        }
 
        // Un handler peut lui-même dispatcher de nouveaux messages
        $this->bus->dispatch(new SendEmailMessage(
            destinataire: $commande->getClient()->getEmail(),
            sujet:        'Votre facture n°' . $commande->getNumero(),
            contenuHtml:  '<p>Veuillez trouver votre facture en pièce jointe.</p>'
        ));
    }
}
