<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class AudioStreamItemRepository extends EntityRepository
{
    const DEFAULT_ALIAS = 'asi';

    public function findLastBySourceQueryBuilder(
        string $source,
        ?QueryBuilder $qb = null,
        ?string $alias = self::DEFAULT_ALIAS
    ): QueryBuilder {
        if (!$qb) {
            $qb = $this->createQueryBuilder($alias);
        }

        $qb
            ->andWhere($qb->expr()->eq(sprintf('%s.source', $alias), ':source'))
            ->orderBy(sprintf('%s.observedAt', $alias), 'DESC')
            ->setMaxResults(1)
            ->setParameter('source', $source)
        ;

        return $qb;
    }

    public function findByDateAndSourceQueryBuilder(
        string $source,
        ?\DateTimeInterface $at,
        ?QueryBuilder $qb = null,
        ?string $alias = self::DEFAULT_ALIAS
    ): QueryBuilder {
        if (!$qb) {
            $qb = $this->createQueryBuilder($alias);
        }

        $qb
            ->andWhere($qb->expr()->eq(sprintf('%s.source', $alias), ':source'))
            ->orderBy(sprintf('%s.observedAt', $alias), 'DESC')
            ->setMaxResults(20)
            ->setParameter('source', $source)
        ;

        if (null !== $at) {
            $fromDate = (clone $at)->modify('-10 minute');
            $toDate = (clone $at)->modify('+10 minute');

            $qb
                ->andWhere($qb->expr()->between(sprintf('%s.observedAt', $alias), ':fromDate', ':toDate'))
                ->setParameter('fromDate', $fromDate)
                ->setParameter('toDate', $toDate)
            ;
        }

        return $qb;
    }
}
