<?php
/**
 * © Julkwel <julienrajerison5@gmail.com>
 *
 * Fokontany Repository.
 */

namespace App\Repository;

use App\Entity\Fokontany;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Fokontany|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fokontany|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fokontany[]    findAll()
 * @method Fokontany[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FokontanyRepository extends ServiceEntityRepository
{
    /**
     * FokontanyRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fokontany::class);
    }

    /**
     * @return Query
     */
    public function findAllFokontany()
    {
        return $this->createQueryBuilder('f')->getQuery();
    }
}
