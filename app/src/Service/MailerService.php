<?php

namespace App\Service;

use App\Entity\Newsletter;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

final class MailerService
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly string $fromEmail = 'arnaud-lefrancois@hotmail.com',
    ) {}

    /**
     * @param iterable<Newsletter> $newsletters
     */
    public function sendAccepted(string $to, iterable $newsletters): void
    {
        $email = (new TemplatedEmail())
            ->from($this->fromEmail)
            ->to($to)
            ->subject('Inscription confirmée')
            ->htmlTemplate('emails/subscription_accepted.html.twig')
            ->context(['newsletters' => $newsletters]);

        $this->mailer->send($email);
    }

    public function sendRefused(string $to): void
    {
        $email = (new TemplatedEmail())
            ->from($this->fromEmail)
            ->to($to)
            ->subject('Inscription refusée')
            ->htmlTemplate('emails/subscription_refused.html.twig');

        $this->mailer->send($email);
    }
}
