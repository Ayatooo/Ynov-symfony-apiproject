<?php

namespace App\Repository;

use App\Entity\RestaurantOwner;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RestaurantOwner>
 *
 * @method RestaurantOwner|null find($id, $lockMode = null, $lockVersion = null)
 * @method RestaurantOwner|null findOneBy(array $criteria, array $orderBy = null)
 * @method RestaurantOwner[]    findAll()
 * @method RestaurantOwner[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RestaurantOwnerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RestaurantOwner::class);
    }

    public function save(RestaurantOwner $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RestaurantOwner $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findWithPagination(int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;

        return $this->createQueryBuilder('u')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->where('u.status = :status')
            ->setParameter('status', "true")
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return RestaurantOwner[] Returns an array of RestaurantOwner objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?RestaurantOwner
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
