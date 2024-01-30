<?php

namespace App\Service;

use App\DTO\PropertyCreationDTO;
use App\Entity\Property;
use App\DTO\PropertyDTO;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;

class PropertyService
{
    private EntityManagerInterface $entityManager;
    private PropertyRepository $propertyRepository;

    public function __construct(EntityManagerInterface $entityManager, PropertyRepository $propertyRepository )
    {
        $this->entityManager = $entityManager;
        $this->propertyRepository = $propertyRepository;
    }

    public function createProperty(PropertyCreationDTO $propertyCreationDTO):Property
    {
        $now = new \DateTime('now');
        $property = new Property();
        $property->setTitle($propertyCreationDTO->title);
        $property->setDescription($propertyCreationDTO->description);
        $property->setPrice($propertyCreationDTO->price); //null
        $property->setLocation($propertyCreationDTO->location);
        $property->setSize($propertyCreationDTO->size);
        $property->setImages($propertyCreationDTO->images);
        $property->setAgentId($propertyCreationDTO->agentId);
        //need to convert to string because of asserts in Entity class
        $property->setCreatedAt($now->format('Y-m-d H:i:s'));
        $property->setUpdatedAt($now->format('Y-m-d H:i:s'));

        $this->entityManager->persist($property);
        $this->entityManager->flush();

        return $property;
    }

    public function getPropertyById($id) //ovde mozda da pretvorim u DTO
    {

        if (!empty($property = $this->entityManager->getRepository(Property::class)->find($id)))
        {
            return $property;
        }

        return null;
    }

    public function getProperties()
    {
        return $this->entityManager->getRepository(Property::class)->findAll();
    }

    public function updateProperty(int $id, PropertyDTO $propertyDTO)
    {
        $now = new \DateTime('now');
        $property = $this->getPropertyById($id);
        if (empty($property)) {
            return null;
        }
        // Update the property's fields with the new values
        $property->setTitle($propertyDTO->title);
        $property->setDescription($propertyDTO->description);
        $property->setPrice($propertyDTO->price);
        $property->setLocation($propertyDTO->location);
        $property->setSize($propertyDTO->size);
        $property->setImages($propertyDTO->images);
        $property->setAgentId($propertyDTO->agentId);
        $property->setUpdatedAt($now->format('Y-m-d H:i:s'));

        $this->entityManager->flush();

        return $property;
    }

    public function deleteProperty($id)
    {

        if (!empty($property = $this->getPropertyById($id))) {

            $this->entityManager->remove($property);
            $this->entityManager->flush();

            return true;
        }

        return false;

    }

    public function searchProperties(PropertySearchDTO $searchDTO): Paginator
    {
        $queryBuilder = $this->propertyRepository->createSearchQueryBuilder($searchDTO);

        return $this->propertyRepository->paginate($queryBuilder, $searchDTO->page, $searchDTO->limit);
    }
}