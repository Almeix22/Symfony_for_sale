<?php

namespace App\Repository;

use App\Entity\Advertisement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Advertisement>
 *
 * @method Advertisement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Advertisement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Advertisement[]    findAll()
 * @method Advertisement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdvertisementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Advertisement::class);
    }

    public function search(string $text = ''): array
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.title LIKE lower(:searchterm)')
            ->setParameter('searchterm', '%'.$text.'%')
            ->orderBy('a.createdAt', 'desc');
        $query = $qb->getQuery();

        return $query->execute();
    }

    public function findAllByDate(): array
    {
        return $this->createQueryBuilder('a')
                ->orderBy('a.createdAt', 'desc')
                ->getQuery()
                ->getResult();
    }

    public function queryAllByDateWithCategory(): \Doctrine\ORM\Query
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.category', 'cat')
            ->addSelect('cat as Category')
            ->orderBy('a.createdAt', 'desc')
            ->getQuery();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneAdvertisementWithCategoryById(int $id): ?Advertisement
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.category', 'cat')
            ->addSelect('cat as Category')
            ->where('a.id = :id')
            ->setParameter('id', $id)
            ->orderBy('a.createdAt', 'ASC')
            ->getQuery()
            ->getOneOrNullResult();
    }

    //    /**
    //     * @return Advertisement[] Returns an array of Advertisement objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Advertisement
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
