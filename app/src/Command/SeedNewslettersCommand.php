<?php

namespace App\Command;

use App\Entity\Newsletter;
use App\Repository\NewsletterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:seed:newsletters',
    description: 'Seed database with default newsletters',
)]
class SeedNewslettersCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly NewsletterRepository $newsletterRepository,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $newsletters = [
            [
                'name' => 'Tech',
                'description' => 'Actualités et tendances tech',
            ],
            [
                'name' => 'Business',
                'description' => 'Stratégie, produit et entrepreneuriat',
            ],
            [
                'name' => 'Lifestyle',
                'description' => 'Bien-être, inspiration et quotidien',
            ],
        ];

        $created = 0;

        foreach ($newsletters as $data) {
            $existing = $this->newsletterRepository->findOneBy([
                'name' => $data['name'],
            ]);

            if ($existing) {
                continue;
            }

            $newsletter = new Newsletter();
            $newsletter->setName($data['name']);
            $newsletter->setDescription($data['description']);

            $this->em->persist($newsletter);
            $created++;
        }

        $this->em->flush();

        if ($created === 0) {
            $io->success('All newsletters already exist.');
        } else {
            $io->success(sprintf('%d newsletters created.', $created));
        }

        return Command::SUCCESS;
    }
}
