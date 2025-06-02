<?php

namespace App\Controller;

use App\Entity\AudioStreamItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/v1', name: 'api_v1_')]
class ApiController extends AbstractController
{
    public function __construct(
        protected EntityManagerInterface $em,
        protected SerializerInterface $serializer,
        protected array $observedIcecastSources
    ) {
    }

    #[Route('/broadcasted_audio_stream', name: 'broadcasted_audio_stream')]
    public function getBroadcastedAudioStream(Request $request): Response
    {
        $resolver = new OptionsResolver();
        $this->configureApiOptions($resolver);
        $options = $resolver->resolve($request->query->all());

        $audioStreamItems = $this->em->getRepository(AudioStreamItem::class)->findBy($options['criteria'], $options['order_by'], $options['limit']);
        $jsonContent = $this->serializer->serialize($audioStreamItems, 'json');

        return JsonResponse::fromJsonString($jsonContent);
    }

    protected function configureApiOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('source')->setAllowedValues('source', array_keys($this->observedIcecastSources))
            ->setDefault('limit', 100)->setAllowedTypes('limit', ['int'])
            ->setDefault('criteria', [])->setNormalizer('criteria', function (Options $options, $value) {
                return [
                    'source' => $options['source'],
                ];
            })
            ->setDefault('order_by', null)->setNormalizer('order_by', function (Options $options, $value) {
                return [
                    'observedAt' => 'DESC',
                ];
            })
        ;
    }
}