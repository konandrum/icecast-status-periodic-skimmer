<?php

namespace App\Command;

use App\Entity\AudioStreamItem;
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
            ->setDescription('Harvest icecast serveur informations based on configured observed sources')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->icecastConfiguredSourcesFactory->buildSources() as $icecastSource) {
            $audioStreamItem = $icecastSource->getAudioStreamItem();

            if ($this->storeAudioStreamItem($audioStreamItem)) {
                $output->writeln(sprintf('<info>[%s | %s] add "%s"</info>',
                    $audioStreamItem->getObservedAt()->format('Y-m-d H:i:s'),
                    $audioStreamItem->getSource(),
                    $audioStreamItem->getTitle()
                ));
            }

            $output->writeln(sprintf('<comment>[%s | %s] no item added</comment>',
                $audioStreamItem->getObservedAt()->format('Y-m-d H:i:s'),
                $audioStreamItem->getSource()
            ));
        }

        $this->entityManager->flush();

        return Command::SUCCESS;
    }

    protected function storeAudioStreamItem(AudioStreamItem $audioStreamItem): bool
    {
        $lastAudioStreamItem = $this->entityManager
            ->getRepository(AudioStreamItem::class)
            ->findLastBySourceQueryBuilder($audioStreamItem->getSource())
            ->getQuery()->getOneOrNullResult()
        ;

        if (null !== $lastAudioStreamItem && $lastAudioStreamItem->getTitle() === $audioStreamItem->getTitle()) {
            return false;
        }

        $this->entityManager->persist($audioStreamItem);

        return true;
    }
}
