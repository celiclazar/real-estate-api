<?php

namespace App\Controller;

use App\DTO\CreatePropertyDTO;
use App\DTO\UpdatePropertyDTO;
use App\Service\PropertyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

class PropertyApiController extends AbstractController
{
    private object $propertyService;

    public function __construct(PropertyService $propertyService)
    {
        $this->propertyService = $propertyService;
    }

    #[Route('api/properties', name: 'app_properties', methods: ['GET'])]
    public function property(Request $request):JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        try {
            return $this->json($this->propertyService->getProperties($page, $limit));
        } catch (\Exception $e) {
            return $this->json(
                ['error' => 'An error occurred: ' . $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('api/properties/{id}', name: 'app_properties_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show($id = null): JsonResponse
    {
        $property = $this->propertyService->getPropertyById($id);

        if ($property === null) {
            return $this->json(
                ['error' => 'Property not found', 'evo me ovde',],
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->json($property);
    }

    #[Route('api/properties/add', name: 'app_properties_add', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreatePropertyDTO $propertyCreationDTO): JsonResponse
    {
        try {
            if ($responseDTO = $this->propertyService->createProperty($propertyCreationDTO)) {
                return $this->json(
                    ['message' => $responseDTO->getMessage(), 'property' => $responseDTO->getData()],
                    Response::HTTP_CREATED // 201 status code
                );
            } else {
                return $this->json(
                    ['error' => $responseDTO->getMessage(), 'errorMessage' => $responseDTO->getError()],
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
    public function update(int $id, #[MapRequestPayload] UpdatePropertyDTO $propertyUpdateDTO): JsonResponse
    {
        try {
            if ($responseDTO = $this->propertyService->updateProperty($id, $propertyUpdateDTO)) {
                return $this->json(
                    ['message' => $responseDTO->getMessage(), 'property' => $responseDTO->getData()],
                    Response::HTTP_CREATED // 201 status code
                );
            } else {
                return $this->json(
                    ['error' => $responseDTO->getMessage(), 'errorMessage' => $responseDTO->getError()],
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

    #[Route('api/properties/{id}', name: 'app_properties_delete', methods: ['DELETE'])]
    public function delete($id): Response
    {
        try {
            $this->propertyService->deleteProperty($id);
            return $this->json(['success' => 'Property deleted'], JsonResponse::HTTP_OK);

        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('api/properties/search', name:'app_properties_search', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        try {
            $title = $request->query->get('title');
            $price = $request->query->get('price');
            $location = $request->query->get('location');
            $size = $request->query->get('size');
            $agentId = $request->query->get('agentId');

            $page = $request->query->getInt('page', 1);
            $limit = $request->query->getInt('limit', 10);

            $properties = $this->propertyService->searchProperties($title, $price, $location, $size, $agentId, $page, $limit);
            return $this->json([
                'data' => $properties,
                'currentPage' => $page,
                'totalItems' => count($properties),
                'totalPages' => ceil(count($properties) / $limit)
            ]);
        } catch (\Exception $e) {
            return $this->json( $e->getMessage());
        }
    }

}