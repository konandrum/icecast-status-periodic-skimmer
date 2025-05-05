<?php

namespace App\Source;

use App\Entity\AudioStreamItem;
use App\Factory\AbstractAudioStreamItemFactory;
use GuzzleHttp\ClientInterface;

class IcecastSource
{
    private string $name;
    private string $scheme;
    private string $host;
    private string $statusPath;
    private string $sourcePath;

    public function __construct(
        private ClientInterface $httpClient
    ) {
    }

    public function getHttpClient(): ClientInterface
    {
        return $this->httpClient;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function setScheme(string $scheme): self
    {
        $this->scheme = $scheme;

        return $this;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function setHost(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    public function getStatusPath(): string
    {
        return $this->statusPath;
    }

    public function setStatusPath(string $statusPath): self
    {
        $this->statusPath = $statusPath;

        return $this;
    }

    public function getSourcePath(): string
    {
        return $this->sourcePath;
    }

    public function setSourcePath(string $sourcePath): self
    {
        $this->sourcePath = $sourcePath;

        return $this;
    }

    public function getUrl(): string
    {
        return sprintf('%s://%s%s',
            $this->getScheme(),
            $this->getHost(),
            $this->getStatusPath()
        );
    }

    public function getAudioStreamItem(): AudioStreamItem
    {
        $response = $this->getHttpClient()->get($this->getUrl());
        $jsonContent = json_decode($response->getBody()->getContents(), true);

        return AbstractAudioStreamItemFactory::createFromArray($jsonContent['icestats']['source'][$this->getSourcePath()]);
    }
}
