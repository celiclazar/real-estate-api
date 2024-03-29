<?php

namespace App\DTO;

class UpdatePropertyDTO
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

        #[Assert\Type(
            type: 'integer',
            message: 'The value {{ value }} is not a valid {{ type }}.',
        )]
        public $size,

        #[Assert\Type('array||null')]
        public $images,

        public $agentId
    )
    {
    }
}
