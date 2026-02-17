<?php

namespace App\Repository;

use App\Entity\Registration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Registration>
 */
class RegistrationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Registration::class);
    }

    /**
     * Find registartions with optional filters
     * @return Registration[] Return an array of Registration objects
     */

    public function findWithFilters(
        ?string $status = null,
        ?int $courseId = null,
        ?\DateTimeInterface $startDate = null,
        ?\DateTimeInterface $endDate = null,
        ): array
    {
        $qb = $this->createQueryBuilder('r')
                ->leftJoin('r.user', 'u')
                ->leftJoin('r.session', 's')
                ->leftJoin('s.course', 'c')
                ->addSelect('u', 's', 'c')
                ->orderBy('r.registeredAt', 'DESC');

        // Filter by status if provided
        if ($status && $status !== 'all') {
            $qb->andWhere('r.status = :status')
                ->setParameter('status', $status);
        }

        // Filter by course if provided
        if ($courseId !== null) {
            $qb->andWhere('c.id = :courseId')
                ->setParameter('courseId', $courseId);
        }

        //Filter by date range if provided
        if ($startDate !== null) {
            $qb->andWhere('s.startTime >= :startDate')
                ->setParameter('startDate', $startDate);
        }

        if ($endDate !== null) {
            // Set end of day (23:59:29)
            $endOfDay = (clone $endDate)->setTime(23, 59, 59);
            $qb->andWhere('s.startTime <= :endDate')
                ->setParameter('endDate', $endOfDay);
        }

        return $qb->getQuery()->getResult();

    }
}
