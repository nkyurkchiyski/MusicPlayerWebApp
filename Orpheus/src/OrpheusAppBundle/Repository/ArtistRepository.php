<?php

namespace OrpheusAppBundle\Repository;

use OrpheusAppBundle\Entity\Artist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Artist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Artist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Artist[]    findAll()
 * @method Artist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArtistRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Artist::class);
    }

    // /**
    //  * @return Artist[] Returns an array of Artist objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */


    public function findOneByName($value): ?Artist
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.name = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function save(Artist $artist)
    {
        try {
            $this->_em->persist($artist);
            $this->_em->flush();
            return true;
        }catch (ORMException $e) {
            return false;
        }

    }

    public function update(Artist $artist)
    {
        try {
            $this->_em->merge($artist);
            $this->_em->flush();
            return true;
        }catch (ORMException $e) {
            return false;
        }

    }

    public function remove(Artist $artist)
    {
        try {
            $this->_em->remove($artist);
            $this->_em->flush();
            return true;
        }catch (ORMException $e) {
            return false;
        }

    }

}
