<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class PropertyResponseDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public readonly string $title,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public readonly string $description,

        #[Assert\Type(
            type: 'integer',
            message: 'The value {{ value }} is not a valid {{ type }}.',
        )]
        public readonly int $price,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public readonly string $location,

        #[Assert\Type(
            type: 'integer',
            message: 'The value {{ value }} is not a valid {{ type }}.',
        )]
        public $size,

        #[Assert\Type('array||null')]
        public $images,

        public $agentId
    ){

    }
}