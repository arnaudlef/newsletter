<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\NewsletterRepository;
use App\Entity\Subscription;
use App\Enum\SubscriptionStatus;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

#[AsCommand(
    name: 'app:send:newsletters',
    description: 'Send newsletters to approved subscriber',
)]
class SendNewslettersCommand extends Command
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly EntityManagerInterface $em,
        private readonly NewsletterRepository $newsletterRepository,
        private readonly string $fromEmail = 'arnaud-lefrancois@hotmail.com',
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $subscriptions = $this->em->getRepository(Subscription::class)->findAll();

        if ($subscriptions !== []) {
            foreach($subscriptions as $subscription) {
                if ($subscription->getStatus() === SubscriptionStatus::ACCEPTED) {
                    $newsletter = $subscription->getNewsletter();
                    $subscriber = $subscription->getSubscriber();

                    $email = (new TemplatedEmail())
                        ->from($this->fromEmail)
                        ->to($subscriber->getEmail())
                        ->subject('Newsletter')
                        ->htmlTemplate('emails/newsletter_description.html.twig')
                        ->context(['newsletter' => $newsletter]);

                    $this->mailer->send($email);
                }
            }
            $io->success('Les newsletters ont bien étés envoyées.');

            return Command::SUCCESS;
        }
        
        $io->success('Il n\'y a pas de newsletter à envoyer.');

        return Command::SUCCESS;
    }
}
