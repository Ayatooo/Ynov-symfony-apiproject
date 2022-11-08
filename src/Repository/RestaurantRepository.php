<?php

namespace App\Repository;

use App\Entity\Restaurant;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Restaurant>
 *
 * @method Restaurant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Restaurant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Restaurant[]    findAll()
 * @method Restaurant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RestaurantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Restaurant::class);
    }

    public function save(Restaurant $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Restaurant $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function findWithPagination(int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;

        return $this->createQueryBuilder('r')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->where('r.status = :status')
            ->setParameter('status', "true")
            ->getQuery()
            ->getResult();
    }

    public function findClosestRestaurant($latitude, $longitude, $distance)
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata(Restaurant::class, 'r');
        $query = $this->getEntityManager()->createNativeQuery(
            'SELECT *,
            (6371 * acos(cos(radians(:latitude)) * cos(radians(r.restaurant_latitude)) * cos(radians(r.restaurant_longitude) - radians(:longitude)) + sin(radians(:latitude)) * sin(radians(r.restaurant_latitude)))) AS restaurant_distance
            FROM restaurant r
            WHERE r.status = "true"
            HAVING restaurant_distance < :distance
            ORDER BY restaurant_distance',
            $rsm
        );
        $query->setParameter('latitude', $latitude);
        $query->setParameter('longitude', $longitude);
        $query->setParameter('distance', $distance);
        return $query->getResult();
    }
}
