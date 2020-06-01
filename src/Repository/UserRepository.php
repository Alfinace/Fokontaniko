<?php
/**
 * © Julkwel <julienrajerison5@gmail.com>
 *
 * Fokontany Repository
 */

namespace App\Repository;

use App\Entity\Fokontany;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    /**
     * UserRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param Fokontany|null $fokontany
     *
     * @return mixed number of user
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getTotalUser(?Fokontany $fokontany)
    {
        $qb = $this->createQueryBuilder('u')
            ->andWhere('u.fokontany = :fokontany')
            ->setParameter('fokontany', $fokontany)
            ->select('count(u.id)');

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param Fokontany|null $fokontany
     *
     * @return Query
     */
    public function findPublic(?Fokontany $fokontany)
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.roles LIKE :roles')
            ->andWhere('u.fokontany = :fokontany')
            ->setParameter('fokontany', $fokontany)
            ->setParameter('roles', '%ROLE_USER%');

        return $qb->getQuery();
    }
}
