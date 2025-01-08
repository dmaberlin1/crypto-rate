<?php

namespace App\Repository;

use App\Entity\CurrencyRate;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class CurrencyRateRepository
{
    private EntityManagerInterface $em;
    private EntityRepository $repository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repository = $this->em->getRepository(CurrencyRate::class);
    }

    /**
     * Save or update the rate for a currency pair.
     *
     * @param string $pair
     * @param float $rate
     */
    public function saveRate(string $pair, float $rate): void
    {
        $rateEntity = $this->repository->findOneBy(['pair' => $pair]);

        if (!$rateEntity) {
            $rateEntity = new CurrencyRate();
            $rateEntity->setPair($pair);
        }

        $rateEntity->setRate($rate);
        $rateEntity->setUpdatedAt(new DateTimeImmutable());  // Using DateTimeImmutable for immutability

        $this->em->persist($rateEntity);
        $this->em->flush();
    }

    /**
     * Get rates by a date range.
     *
     * @param string $pair
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return CurrencyRate[]
     */
    public function getRatesByDateRange(string $pair, \DateTime $startDate, \DateTime $endDate): array
    {
        return $this->repository->createQueryBuilder('r')
            ->where('r.pair = :pair')
            ->andWhere('r.updatedAt BETWEEN :start AND :end')
            ->setParameters([
                'pair' => $pair,
                'start' => $startDate,
                'end' => $endDate,
            ])
            ->getQuery()
            ->getResult();
    }
}