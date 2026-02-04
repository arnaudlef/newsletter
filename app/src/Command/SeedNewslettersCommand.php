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
                'description' => "Les métiers de la tech englobent un vaste éventail de professions axées sur la conception, le développement, la gestion et l'optimisation de technologies de l'information et de la communication. Les développeurs de logiciels jouent un rôle central en créant des applications, des systèmes et des solutions innovantes pour répondre aux besoins croissants de la société numérique. Les ingénieurs en informatique contribuent à la conception et à la maintenance des infrastructures technologiques, tandis que les experts en cybersécurité assurent la protection des données et des systèmes contre les menaces potentielles. De plus, les professionnels du machine learning et de l'intelligence artificielle explorent les possibilités offertes par ces domaines émergents pour créer des applications intelligentes et automatisées.",
            ],
            [
                'name' => 'Business',
                'description' => "Un business désigne une entreprise/organisation qui a une activité commerciale, industrielle ou professionnelle. Ces business peuvent être à but lucratif ou non. Il s’agit de la définition principale qui réfère à un type d’organisation, mais il existe aussi une définition qui définit le business comme l’effort produit par des équipes au sein d’une entreprise pour développer son activité et ses ventes dans un but lucratif.Il y a donc la définition du business concernant les organisations et la définition du business concernant les activités menées au sein des organisations.",
            ],
            [
                'name' => 'Lifestyle',
                'description' => "Le mode de vie, aussi appelé style de vie ou art de vivre, parfois désigné par l'anglicisme lifestyle, est la manière de vivre, d'être et de penser d'une personne ou d'un groupe d'individus. C'est leur comportement quotidien, leur façon de vivre autour et pour certaines valeurs.",
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
