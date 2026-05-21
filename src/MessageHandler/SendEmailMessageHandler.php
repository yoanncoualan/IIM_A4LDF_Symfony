<?php
 
namespace App\MessageHandler;
 
use App\Message\SendEmailMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;
 
#[AsMessageHandler]
class SendEmailMessageHandler
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly LoggerInterface $logger,
    ) {}
 
    // __invoke permet de rendre la classe exécutable comme une fonction
    // __invoke est appelé automatiquement par Messenger
    public function __invoke(SendEmailMessage $message): void
    {
        $this->logger->error('Traitement envoi email', [
            'destinataire' => $message->destinataire,
            'sujet'        => $message->sujet,
        ]);
 
        /*
            // Envoie un email
            $email = (new Email())
                ->to($message->destinataire)
                ->subject($message->sujet)
                ->html($message->contenuHtml);
 
            if ($message->fichierJoint) {
                $email->attachFromPath($message->fichierJoint);
            }
 
            $this->mailer->send($email);
        */
 
        $this->logger->error('Email envoyé avec succès', [
            'destinataire' => $message->destinataire,
        ]);
    }
}