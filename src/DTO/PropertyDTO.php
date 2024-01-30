<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class PropertyDTO
{
    #[Assert\NotBlank]
    public $title;
    #[Assert\NotBlank]
    public $description;
    #[Assert\Type(
        type: 'integer',
        message: 'The value {{ value }} is not a valid {{ type }}.',
    )]
    public $price;
    #[Assert\NotBlank]
    public $location;
    #[Assert\Type(
        type: 'integer',
        message: 'The value {{ value }} is not a valid {{ type }}.',
    )]
    public $size;

    public $images;

    public $agentId;

    public function __construct(array $data = [])
    {
        $this->title = $data['title'] ?? null;
        $this->description= $data['description'] ?? null;
        $this->price = $data['price'] ?? null;
        $this->location = $data['location'] ?? null;
        $this->size = $data['size'] ?? null;
        $this->images = $data['images'] ?? null;
        $this->agentId = $data['agentId'] ?? null;
    }
}
