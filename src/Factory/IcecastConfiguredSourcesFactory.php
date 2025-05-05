<?php

namespace App\Factory;

use App\Source\IcecastSource;
use GuzzleHttp\ClientInterface;

class IcecastConfiguredSourcesFactory
{
    public function __construct(
        private array $observedIcecastSources,
        private ClientInterface $icecastClient
    ) {
    }

    public function getObservedIcecastSources(): array
    {
        return $this->observedIcecastSources;
    }

    public function buildSources(): array
    {
        $sources = [];

        foreach ($this->getObservedIcecastSources() as $name => $observedIcecastSource) {
            $sources[] = (new IcecastSource($this->icecastClient))
                ->setName($name)
                ->setScheme($observedIcecastSource['scheme'])
                ->setHost($observedIcecastSource['host'])
                ->setStatusPath($observedIcecastSource['status_path'])
                ->setSourcePath($observedIcecastSource['source_path'])
            ;
        }

        return $sources;
    }
}
