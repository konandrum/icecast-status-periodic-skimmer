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
    private string $serverName;

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

    public function getServerName(): string
    {
        return $this->serverName;
    }

    public function setServerName(string $serverName): self
    {
        $this->serverName = $serverName;

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

        $dataSource = self::extractSourceData($jsonContent['icestats']['source'], $this->getServerName());

        return AbstractAudioStreamItemFactory::createFromArray(array_merge(
            ['source' => $this->getName()],
            $dataSource
        ));
    }

    public static function extractSourceData(array $dataSources, string $serverName): array
    {
        foreach ($dataSources as $dataSource) {
            if ($serverName === $dataSource['server_name']) {
                return $dataSource;
            }
        }

        throw new \Exception(sprintf('No server_name found with value "%s"', $serverName));
    }
}
