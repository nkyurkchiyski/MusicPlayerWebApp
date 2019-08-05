<?php

namespace OrpheusAppBundle\Repository;

use OrpheusAppBundle\Entity\Playlist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Playlist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Playlist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Playlist[]    findAll()
 * @method Playlist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaylistRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Playlist::class);
    }

    // /**
    //  * @return Playlist[] Returns an array of Playlist objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Playlist
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function save(Playlist $playlist)
    {
        try {
            $this->_em->persist($playlist);
            $this->_em->flush();
            return true;
        }catch (ORMException $e) {
            return false;
        }

    }

    public function update(Playlist $playlist)
    {
        try {
            $this->_em->merge($playlist);
            $this->_em->flush();
            return true;
        }catch (ORMException $e) {
            return false;
        }

    }

    public function remove(Playlist $playlist)
    {
        try {
            $this->_em->remove($playlist);
            $this->_em->flush();
            return true;
        }catch (ORMException $e) {
            return false;
        }

    }
}
