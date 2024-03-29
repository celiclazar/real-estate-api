<?php

namespace App\Controller;

use App\DTO\LoginUserRequestDTO;
use App\DTO\RegisterUserRequestDTO;
use App\Service\AuthenticationUserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private AuthenticationUserService $authenticationUserService;

    public function __construct(AuthenticationUserService $authenticationUserService)
    {
        $this->authenticationUserService = $authenticationUserService;
    }

    #[Route('/api/login', name: 'app_login', methods: ['POST'])]
    public function login(#[MapRequestPayload] LoginUserRequestDTO $loginRequestDTO): JsonResponse
    {
        try {
            $token = $this->authenticationUserService->login($loginRequestDTO);

            return new JsonResponse(['token' => $token]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/register', name: 'register', methods: ['POST'])]
    public function register(#[MapRequestPayload] RegisterUserRequestDTO $registerRequestDTO): JsonResponse {
        try {
            if ($responseDTO = $this->authenticationUserService->registerUser($registerRequestDTO)) {
                return $this->json(
                    ['message' => $responseDTO->getMessage(), 'user' => $responseDTO->getData()],
                    Response::HTTP_CREATED
                );
            } else {
                return $this->json(
                    ['error' => $responseDTO->getMessage(), 'errorMessage' => $responseDTO->getError()],
                    Response::HTTP_BAD_REQUEST
                );
            }
        } catch (\Exception $e) {
            return $this->json(
              ['error' => 'An error occured: ' . $e->getMessage()],
              Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
