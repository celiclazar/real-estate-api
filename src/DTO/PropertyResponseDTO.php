<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class PropertyResponseDTO
{
    public function __construct(
        public int $id,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $title,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $description,

        #[Assert\Type(
            type: 'integer',
            message: 'The value {{ value }} is not a valid {{ type }}.',
        )]
        public int $price,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $location,

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