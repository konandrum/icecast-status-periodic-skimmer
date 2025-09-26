<?php

namespace App\Factory;

use App\Entity\AudioStreamItem;

abstract class AbstractAudioStreamItemFactory
{
    public static function createFromJson(string $json): AudioStreamItem
    {
        return self::createFromArray(json_decode($json, true));
    }

    public static function createFromArray(array $data): AudioStreamItem
    {
        if (!isset($data['title'])) {
            throw new \Exception(sprintf('Missing "title" information in "%s" data source', $data['source']));
        }

        return (new AudioStreamItem())
            ->setSource($data['source'])
            ->setServerName($data['server_name'])
            ->setTitle($data['title'])
            ->setListenerCounter($data['listeners'])
            ->setObservedAt(new \DateTime('now'))
        ;
    }
}
