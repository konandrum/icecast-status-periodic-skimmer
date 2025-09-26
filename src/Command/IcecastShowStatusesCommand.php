<?php

namespace App\Command;

use App\Entity\AudioStreamItem;
use App\Factory\IcecastConfiguredSourcesFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class IcecastShowStatusesCommand extends Command
{
    public function __construct(
        private IcecastConfiguredSourcesFactory $icecastConfiguredSourcesFactory
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('icecast:show:statuses')
            ->setDescription('Show icecast serveur informations based on configured observed sources')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->icecastConfiguredSourcesFactory->buildSources() as $icecastSource) {
            try {
                $audioStreamItem = $icecastSource->getAudioStreamItem();
            } catch (\Exception $e) {
                $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

                continue;
            }

            $output->writeln(sprintf('<info>[%s | %s] "%s"</info>',
                $audioStreamItem->getObservedAt()->format('Y-m-d H:i:s'),
                $audioStreamItem->getSource(),
                $audioStreamItem->getTitle()
            ));
        }

        return Command::SUCCESS;
    }
}
