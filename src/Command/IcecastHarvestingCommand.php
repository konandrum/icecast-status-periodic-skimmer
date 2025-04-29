<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\ClientInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class IcecastHarvestingCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ClientInterface $icecastClient
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('icecast:harvesting:run')
            ->setDescription('Harvest icecast serveur informations')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $response = $this->icecastClient->get('/status-json.xsl');
            $jsonStatus = json_decode($response->getBody()->getContents(), true);
            dd($jsonStatus);
        } catch (\Exception $e) {
            return Command::ERROR;
        }

        return Command::SUCCESS;
    }
}