<?php

namespace App\Repository;

use App\DTO\PropertySearchDTO;
use App\Entity\Property;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function deleteProperty(Property $propertyEntity)
    {
        $this->entityManager->remove($propertyEntity);
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

    public function findPaginatedProperties(int $limit, int $offset): array
    {
        return $this->createQueryBuilder('p')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    public function searchPaginatedProperties(?string $title, ?string $price, ?string $location, ?string $size, ?string $agentId, int $limit, int $offset): array
    {
        $queryBuilder = $this->createQueryBuilder('p');
        $searchParameters = [
            'title' => $title,
            'price' => $price,
            'location' => $location,
            'size' => $size,
            'agentId' => $agentId,
        ];

        foreach ($searchParameters as $field => $value) {
            if ($value) {
                $queryBuilder->andWhere("p.$field LIKE :$field")
                    ->setParameter($field, '%' . $value . '%');
            }
        }
        $queryBuilder->setMaxResults($limit)->setFirstResult($offset);

        return $queryBuilder->getQuery()->getResult();
    }
}
