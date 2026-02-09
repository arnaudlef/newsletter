<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\NewsletterRepository;
use App\Repository\SubscriptionRepository;
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
use Symfony\Component\Console\Attribute\Argument;

#[AsCommand(
    name: 'app:send:newsletter',
    description: 'Send selected newsletter to associated subscriber',
)]
class SendNewsletterCommand extends Command
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly NewsletterRepository $newsletterRepository,
        private readonly SubscriptionRepository $subscriptionRepository,
        private readonly string $fromEmail = 'arnaud-lefrancois@hotmail.com',
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('id', InputArgument::REQUIRED, 'id of newsletter')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $id = (int) $input->getArgument('id');

        $counter = 0;

        $newsletter = $this->newsletterRepository->findById($id);

        $subscriptions = $this->subscriptionRepository->findApprovedByNewsletter($newsletter);

        if ($subscriptions !== []) {
            foreach($subscriptions as $subscription) {
                $newsletter = $subscription->getNewsletter();
                $subscriber = $subscription->getSubscriber();

                $email = (new TemplatedEmail())
                    ->from($this->fromEmail)
                    ->to($subscriber->getEmail())
                    ->subject('Newsletter')
                    ->htmlTemplate('emails/newsletter_description.html.twig')
                    ->context(['newsletter' => $newsletter, 'subscription' => $subscription]);

                $this->mailer->send($email);

                $counter++;
            }
            
            $io->success('Les newsletters ont bien étés envoyéeeeeees.');
            return Command::SUCCESS;
        }
        
        $io->success('Il n\'y a pas de newsletter à envoyer.');

        return Command::SUCCESS;
    }
}
