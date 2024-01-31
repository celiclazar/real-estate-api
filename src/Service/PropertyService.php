<?php

namespace App\Service;

use App\DTO\CreatePropertyResponseDTO;
use App\DTO\PropertyCreationDTO;
use App\Entity\Property as PropertyEntity;
use App\Repository\PropertyRepository;

class PropertyService
{
    private PropertyRepository $propertyRepository;

    public function __construct( PropertyRepository $propertyRepository )
    {
        $this->propertyRepository = $propertyRepository;
    }

    public function createProperty(PropertyCreationDTO $propertyCreationDTO):CreatePropertyResponseDTO
    {
        try {
            $propertyEntity = $this->convertDTOToEntity($propertyCreationDTO);
            $this->propertyRepository->saveProperty($propertyEntity);

            return new CreatePropertyResponseDTO(true, 'Property created successfully', $propertyEntity);

        } catch (\Exception $e) {
            return new CreatePropertyResponseDTO(false, 'Failed to create property', null, $e->getMessage());
        }
    }

    public function getPropertyById($id)
    {

        if (!empty($property = $this->entityManager->getRepository(PropertyEntity::class)->find($id)))
        {
            return $property;
        }

        return null;
    }

    public function getProperties()
    {
        return $this->entityManager->getRepository(PropertyEntity::class)->findAll();
    }

    public function updateProperty(int $id, PropertyCreationDTO $propertyCreationDTO)
    {
        $now = new \DateTime('now');
        $property = $this->getPropertyById($id);
        if (empty($property)) {
            return null;
        }
        // Update the property's fields with the new values
        $property->setTitle($propertyCreationDTO->title);
        $property->setDescription($propertyCreationDTO->description);
        $property->setPrice($propertyCreationDTO->price);
        $property->setLocation($propertyCreationDTO->location);
        $property->setSize($propertyCreationDTO->size);
        $property->setImages($propertyCreationDTO->images);
        $property->setAgentId($propertyCreationDTO->agentId);
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

//    public function searchProperties(PropertySearchDTO $searchDTO): Paginator
//    {
//        $queryBuilder = $this->propertyRepository->createSearchQueryBuilder($searchDTO);
//
//        return $this->propertyRepository->paginate($queryBuilder, $searchDTO->page, $searchDTO->limit);
//    }

    protected function convertDTOToEntity(PropertyCreationDTO $propertyCreationDTO):PropertyEntity
    {
        $property = new PropertyEntity();
        $now = new \DateTime('now');
        $property->setTitle($propertyCreationDTO->title);
        $property->setDescription($propertyCreationDTO->description);
        $property->setPrice($propertyCreationDTO->price); //null
        $property->setLocation($propertyCreationDTO->location);
        $property->setSize($propertyCreationDTO->size);
        $property->setImages($propertyCreationDTO->images);
        $property->setAgentId($propertyCreationDTO->agentId);
        $property->setCreatedAt($now->format('Y-m-d H:i:s'));
        $property->setUpdatedAt($now->format('Y-m-d H:i:s'));

        return $property;
    }
}