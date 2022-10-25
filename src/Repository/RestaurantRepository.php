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

    //    /**
    //     * @return Restaurant[] Returns an array of Restaurant objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Restaurant
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

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
        $sql = "SELECT *, ( 6371 * acos( cos( radians(:latitude) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(:longitude) ) + sin( radians(:latitude) ) * sin( radians( latitude ) ) ) ) AS distance FROM restaurant HAVING distance < :distance ORDER BY distance LIMIT 0 , 20";
        //transformer la requete en requete doctrine
        $query = $this->getEntityManager()->getConnection()->prepare($sql);
        $query->execute([
            'latitude' => $latitude,
            'longitude' => $longitude,
            'distance' => $distance
        ]);
        return $query->fetchAll();
        // $conn = $this->getEntityManager()->getConnection();
        // $sql = "SELECT *, ( 6371 * acos( cos( radians(:latitude) ) * cos( radians( restaurant_latitude ) ) * cos( radians( restaurant_longitude ) - radians(:longitude) ) + sin( radians(:latitude) ) * sin( radians( restaurant_latitude ) ) ) ) AS distance FROM restaurant HAVING distance < :distance ORDER BY distance LIMIT 0 , 20";
        // $stmt = $conn->prepare($sql);
        // $stmt->executeQuery(['latitude' => $latitude, 'longitude' => $longitude, 'distance' => $distance]);

        // return $stmt->fetchAll();

        $rsm = new ResultSetMapping;
        $entityManager = $this->getEntityManager();
        $rsm = new ResultSetMappingBuilder($entityManager);
        $rsm->addRootEntityFromClassMetadata('App\Entity\Restaurant', 'r');

        // $rsm->addEntityResult('App\Entity\Restaurant', 'r');
        // $rsm->addFieldResult('r', 'id', 'id');
        // $rsm->addFieldResult('r', 'restaurant_name', 'restaurantName');
        // $rsm->addFieldResult('r', 'restaurant_description', 'restaurant_description');
        // $rsm->addFieldResult('r', 'restaurant_latitude', 'restaurant_latitude');
        // $rsm->addFieldResult('r', 'restaurant_longitude', 'restaurant_longitude');
        // $rsm->addFieldResult('r', 'restaurant_address', 'restaurant_address');
        // $rsm->addFieldResult('r', 'restaurant_phone', 'restaurant_phone');

        $query = $this->_em->createNativeQuery('SELECT id FROM restaurant', $rsm);
        // $query->setParameter(1, 'romanb');

        $users = $query->getResult();
        return $users;
    }
}
