<?php

namespace App\Repository;

use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Session>
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    /**
     * Find sessions with optional Filters
     * @return Session[] Returns an array of Session objects
     */
    public function findWithFilters(?int $courseId = null, ?\DateTimeInterface $date =null): array
    {
        $qb = $this->createQueryBuilder('s')
            ->leftJoin('s.course', 'c')
            ->addSelect('c')
            ->where('s.status = :status')
            ->setParameter('status', 'scheduled')
            ->andWhere('s.startTime > :now')
            ->setParameter('now', new \DateTime('now', new \DateTimeZone('Europe/Paris')))
            ->orderBy('s.startTime', 'ASC');

        // Filter by course if provided
        if ($courseId !== null) {
            $qb->andWhere('c.id = :courseId')
                ->setParameter('courseId', $courseId);
        }

        // Filter by date if provided
        if ($date !== null) {
            $startOfDay = (clone $date)->setTime(0, 0, 0);
            $endOfDay = (clone $date)->setTime(23, 59, 59);

            $qb->andWhere('s.startTime BETWEEN :startOfDay AND :endOfDay')
                ->setParameter('startOfDay', $startOfDay)
                ->setParameter('endOfDay', $endOfDay);
        }

        return $qb->getQuery()->getResult();
    }
}
