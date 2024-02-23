<?php

namespace App\Service;

use App\DTO\CreateUserResponseDTO;
use App\DTO\LoginUserRequestDTO;
use App\DTO\RegisterUserRequestDTO;
use App\DTO\RegisterUserResponseDTO;
use App\Repository\UserRepository;
use App\Entity\User as UserEntity;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AuthenticationUserService
{
    private UserRepository $userRepository;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher
    ){

        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    public function login(LoginUserRequestDTO $loginRequestDto): string
    {
        $user = $this->userRepository->findOneByEmail($loginRequestDto->email);

        if (!$user) {
            throw new BadCredentialsException('Invalid email or password.');
        }

        if (!password_verify($loginRequestDto->password, $user->getPassword())) {
            throw new BadCredentialsException('Invalid email or password.');
        }

        return $this->jwtManager->create($user);
    }

    public function registerUser(RegisterUserRequestDTO $registerUserRequestDTO): RegisterUserResponseDTO
    {
        try {
            $userEntity = $this->convertRegisterUserRequestDTOToEntity($registerUserRequestDTO);
            $this->userRepository->registerUser($userEntity);
            $userResponseDTO = $this->convertEntityToCreateUserResponseDTO($userEntity);

            return new RegisterUserResponseDTO(true, 'User created successfully', $userResponseDTO);

        } catch (\Exception $e) {
            return new RegisterUserResponseDTO(false, 'Failed to create user.', null, $e->getMessage());
        }
    }

    public function convertRegisterUserRequestDTOToEntity(RegisterUserRequestDTO $registerUserRequestDTO): UserEntity
    {
        $user = new UserEntity();
        $user->setEmail($registerUserRequestDTO->email);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $registerUserRequestDTO->password);
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_USER']);

        return $user;
    }

    public function convertEntityToCreateUserResponseDTO(UserEntity $user): CreateUserResponseDTO
    {
        $userResponseDTO = new CreateUserResponseDTO(
            $user->getId(),
            $user->getEmail()
        );

        return $userResponseDTO;
    }
}