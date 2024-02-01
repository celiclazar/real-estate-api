<?php

namespace App\Repository;

use App\DTO\PropertySearchDTO;
use App\Entity\Property;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;



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
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Property::class);
        $this->entityManager = $entityManager;
    }

    public function saveProperty(Property $propertyEntity):void
    {
            $this->entityManager->persist($propertyEntity);
            $this->entityManager->flush();
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
