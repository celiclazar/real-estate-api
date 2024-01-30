<?php

namespace App\Controller;

use App\DTO\PropertyCreationDTO;
use App\Service\PropertyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\DTO\PropertyDTO;
use App\Validator\PropertyValidator;

use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

class PropertyApiController extends AbstractController
{
    private $propertyService;
    private $propertyValidator;

    public function __construct(PropertyService $propertyService, PropertyValidator $propertyValidator  )
    {
        $this->propertyService = $propertyService;
        $this->propertyValidator = $propertyValidator;
    }

    #[Route('api/properties', name: 'app_properties', methods: ['GET'])]
    public function property(): JsonResponse
    {
        $properties = $this->propertyService->getProperties();
        return $this->json([
            'properties' => $properties
        ]);
    }

    #[Route('api/properties/{id}', name: 'app_properties_show', methods: ['GET'])]
    public function show($id = null): JsonResponse
    {
        $property = $this->propertyService->getPropertyById($id);

        if (empty($property)){
            return $this->json(['error' => 'Property not found'], 404);
        }

        return $this->json($property, 200, []);
    }

    #[Route('/api/search', name: 'api_properties_search', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        $searchDTO = new PropertySearchDTO(
            $request->query->get('title'),
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 10)
        );

        $paginator = $this->propertyService->searchProperties($searchDTO);

        // You can also convert paginator to array if needed
        $properties = iterator_to_array($paginator);

        return $this->json([
            'data' => $properties,
            'currentPage' => $searchDTO->page,
            'totalItems' => count($paginator),
            'totalPages' => ceil(count($paginator) / $searchDTO->limit)
        ]);
    }

    #[Route('api/properties/add', name: 'app_properties', methods: ['POST'])]
    public function create(#[MapRequestPayload] PropertyCreationDTO $propertyCreationDTO): JsonResponse
    {
        try {
            if ($property = $this->propertyService->createProperty($propertyCreationDTO)) {
                return $this->json(
                    ['message' => 'Property created successfully', 'propertyId' => $property->getId()],
                    Response::HTTP_CREATED // 201 status code
                );
            } else {
                return $this->json(
                    ['error' => 'Property creation failed'],
                    Response::HTTP_BAD_REQUEST // 400 status code
                );
            }
        } catch (\Exception $e) {
            return $this->json(
                ['error' => 'An error occurred: ' . $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR // 500 status code
            );
        }
    }

    #[Route('api/properties/{id?}', name: 'app_properties_update', methods: ['PUT'])]
    public function update(int $id, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $propertyDTO = new PropertyDTO($data);
        $errors = $this->propertyValidator->validate($propertyDTO);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }

            return $this->json([
                'errors' => $errorMessages
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $updatedProperty = $this->propertyService->updateProperty($id, $propertyDTO);

            if (empty($updatedProperty)) {
                return $this->json(['error' => 'Property not found'], Response::HTTP_NOT_FOUND);
            }

            return $this->json([
                'success' => 'Property updated',
                'id' => $updatedProperty->getId()
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('api/properties/{id}', name: 'app_properties_delete', methods: ['DELETE'])]
    public function delete($id): Response
    {
        try {
            $property = $this->propertyService->deleteProperty($id);

            if ($property) {
                return $this->json(['success' => 'Property deleted'], JsonResponse::HTTP_OK);
            } else {
                return $this->json(['error' => 'Property not found'], JsonResponse::HTTP_NOT_FOUND);
            }
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

}

