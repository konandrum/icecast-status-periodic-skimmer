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
        return (new AudioStreamItem())
            ->setSource($data['source'])
            ->setName($data['name'])
            ->setTitle($data['title'])
            ->setObservedAt(new \DateTime('now'))
        ;
    }
}
