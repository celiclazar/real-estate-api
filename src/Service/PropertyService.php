<?php

namespace App\Service;

use App\DTO\CreatePropertyResponseDTO;
use App\DTO\CreatePropertyDTO;
use App\DTO\PropertyResponseDTO;
use App\DTO\UpdatePropertyDTO;
use App\DTO\UpdatePropertyResponseDTO;
use App\Entity\Property as PropertyEntity;
use App\Repository\PropertyRepository;

class PropertyService
{
    private PropertyRepository $propertyRepository;

    public function __construct( PropertyRepository $propertyRepository )
    {
        $this->propertyRepository = $propertyRepository;
    }

    public function createProperty(CreatePropertyDTO $propertyCreationDTO):CreatePropertyResponseDTO
    {
        try {
            $now = new \DateTime('now');
            $propertyEntity = $this->convertCreationDTOToEntity($propertyCreationDTO);
            $propertyEntity->setCreatedAt($now->format('Y-m-d H:i:s'));
            $propertyEntity->setUpdatedAt($now->format('Y-m-d H:i:s'));
            $this->propertyRepository->saveProperty($propertyEntity);

            return new CreatePropertyResponseDTO(true, 'Property created successfully', $propertyEntity);

        } catch (\Exception $e) {
            return new CreatePropertyResponseDTO(false, 'Failed to create property', null, $e->getMessage());
        }
    }

    public function getPropertyById($id)
    {
        if (!empty($property = $this->propertyRepository->find($id)))
        {
            return $this->convertEntityToCreationDTO($property);
        }

        return null;
    }

    public function getProperties(int $page = 1, int $limit = 10)
    {
        $offset = ($page - 1) * $limit;

        return array_map(
            [$this, 'convertEntityToCreationDTO'],
            $this->propertyRepository->findPaginatedProperties($limit, $offset));
    }

    public function updateProperty(int $id, UpdatePropertyDTO $updatePropertyDTO)
    {
        try {
            $now = new \DateTime('now');
            $propertyEntity = $this->convertUpdateDTOToEntity($id, $updatePropertyDTO);
            $propertyEntity->setUpdatedAt($now->format('Y-m-d H:i:s'));
            $this->propertyRepository->saveProperty($propertyEntity);

            return new UpdatePropertyResponseDTO(true, 'Property updated successfully', $propertyEntity);
        } catch (\Exception $e) {
            return new UpdatePropertyResponseDTO(false, 'Updating failed', null, $e->getMessage());
        }
    }

    public function deleteProperty($id)
    {
        $property = $this->getPropertyById($id);

        return $this->propertyRepository->deleteProperty($property);
    }

    protected function convertCreationDTOToEntity(CreatePropertyDTO $propertyCreationDTO):PropertyEntity
    {
        $property = new PropertyEntity();
        $property->setTitle($propertyCreationDTO->title);
        $property->setDescription($propertyCreationDTO->description);
        $property->setPrice($propertyCreationDTO->price);
        $property->setLocation($propertyCreationDTO->location);
        $property->setSize($propertyCreationDTO->size);
        $property->setImages($propertyCreationDTO->images);
        $property->setAgentId($propertyCreationDTO->agentId);

        return $property;
    }

    protected function convertEntityToCreationDTO(PropertyEntity $propertyEntity): PropertyResponseDTO
    {
        return new PropertyResponseDTO(
            $id = $propertyEntity->getId(),
            $title = $propertyEntity->getTitle(),
            $description = $propertyEntity->getDescription(),
            $price = $propertyEntity->getPrice(),
            $location = $propertyEntity->getLocation(),
            $size = $propertyEntity->getSize(),
            $images = $propertyEntity->getImages(),
            $agentId = $propertyEntity->getAgentId(),
        );
    }

    protected function convertUpdateDTOToEntity(int $id, UpdatePropertyDTO $propertyUpdateDTO):PropertyEntity
    {
        $property = $this->propertyRepository->find($id);
        $property->setTitle($propertyUpdateDTO->title);
        $property->setDescription($propertyUpdateDTO->description);
        $property->setPrice($propertyUpdateDTO->price);
        $property->setSize($propertyUpdateDTO->size);
        $property->setImages($propertyUpdateDTO->images);
        $property->setAgentId($propertyUpdateDTO->agentId);

        return $property;
    }
}