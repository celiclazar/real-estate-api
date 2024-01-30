<?php

namespace App\Repository;

use App\DTO\PropertySearchDTO;
use App\Entity\Property;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<Property>
 *
 * @method Property|null find($id, $lockMode = null, $lockVersion = null)
 * @method Property|null findOneBy(array $criteria, array $orderBy = null)
 * @method Property[]    findAll()
 * @method Property[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }

    public function createSearchQueryBuilder(PropertySearchDTO $propertySearchDTO): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('p');

        if ($propertySearchDTO->title) {
            $queryBuilder->andWhere('p.title LIKE :title')
                ->setParameter('title', '%' . $propertySearchDTO->title . '%');
        }

        return $queryBuilder;
    }

    public function paginate(QueryBuilder $queryBuilder, int $page, int $limit): Paginator
    {
        $queryBuilder->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return new Paginator($queryBuilder->getQuery());
    }

}
