<?php

namespace App\Repository;

use App\Entity\ModeleLocation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ModeleLocation>
 */
class ModeleLocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModeleLocation::class);
    }

    //    /**
    //     * @return ModeleLocation[] Returns an array of ModeleLocation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ModeleLocation
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function searchCollection($search, $category, $price, $page, $limit)
{
    $qb = $this->createQueryBuilder('m');

    if ($search) {
        $qb->andWhere('m.libelle LIKE :search')
           ->setParameter('search', "%$search%");
    }

    if ($category) {
        $qb->andWhere('m.type = :cat')
           ->setParameter('cat', $category);
    }

    if ($price) {
        if ($price === '0-50') {
            $qb->andWhere('m.prixLocation <= 50');
        } elseif ($price === '50-100') {
            $qb->andWhere('m.prixLocation BETWEEN 50 AND 100');
        } elseif ($price === '100-150') {
            $qb->andWhere('m.prixLocation BETWEEN 100 AND 150');
        } elseif ($price === '150+') {
            $qb->andWhere('m.prixLocation >= 150');
        }
    }

    // Pagination
    $qb->setFirstResult(($page - 1) * $limit)
       ->setMaxResults($limit);

    $items = $qb->getQuery()->getResult();

    // Total
    $total = (clone $qb)->select('COUNT(m.id)')->resetDQLPart('orderBy')->getQuery()->getSingleScalarResult();

    return [
        'items' => $items,
        'pagination' => [
            'currentPage' => $page,
            'totalPages' => ceil($total / $limit),
        ]
    ];
}

}
