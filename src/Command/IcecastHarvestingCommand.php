<?php

namespace App\Command;

use App\Factory\IcecastConfiguredSourcesFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class IcecastHarvestingCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private IcecastConfiguredSourcesFactory $icecastConfiguredSourcesFactory
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
        foreach ($this->icecastConfiguredSourcesFactory->buildSources() as $icecastSource) {
            dump($icecastSource->getAudioStreamItem());
        }

        dd('ok');
        try {
        } catch (\Exception $e) {
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
