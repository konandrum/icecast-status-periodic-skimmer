<?php

namespace App\Entity;

use App\Repository\AudioStreamItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: AudioStreamItemRepository::class)]
#[ORM\Index(name: 'search_idx', columns: ['source', 'observed_at'])]
class AudioStreamItem
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id;

    #[ORM\Column(length: 64, nullable: false)]
    private string $source;

    #[ORM\Column(length: 255, nullable: false)]
    private string $serverName;

    #[ORM\Column(length: 255, nullable: false)]
    private string $title;

    #[ORM\Column(type: 'integer', nullable: true)]
    private int $listenerCounter;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private \DateTime $observedAt;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;

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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getListenerCounter(): int
    {
        return $this->listenerCounter;
    }

    public function setListenerCounter(int $listenerCounter): self
    {
        $this->listenerCounter = $listenerCounter;

        return $this;
    }

    public function getObservedAt(): \DateTime
    {
        return $this->observedAt;
    }

    public function setObservedAt(\DateTime $observedAt): self
    {
        $this->observedAt = $observedAt;

        return $this;
    }
}
