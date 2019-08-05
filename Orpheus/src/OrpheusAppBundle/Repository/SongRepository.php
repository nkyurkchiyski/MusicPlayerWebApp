<?php

namespace OrpheusAppBundle\Repository;

use OrpheusAppBundle\Entity\Song;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Song|null find($id, $lockMode = null, $lockVersion = null)
 * @method Song|null findOneBy(array $criteria, array $orderBy = null)
 * @method Song[]    findAll()
 * @method Song[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SongRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Song::class);
    }

    // /**
    //  * @return Song[] Returns an array of Song objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Song
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function save(Song $song)
    {
        try {
            $this->_em->persist($song);
            $this->_em->flush();
            return true;
        }catch (ORMException $e) {
            return false;
        }

    }

    public function update(Song $song)
    {
        try {
            $this->_em->merge($song);
            $this->_em->flush();
            return true;
        }catch (ORMException $e) {
            return false;
        }

    }

    public function remove(Song $song)
    {
        try {
            $this->_em->remove($song);
            $this->_em->flush();
            return true;
        }catch (ORMException $e) {
            return false;
        }

    }
}
