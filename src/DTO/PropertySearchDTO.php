<?php

namespace App\DTO;
class PropertySearchDTO
{
    public ?string $title;
    public ?int $page;
    public ?int $limit;
    // Additional fields can be added for other search criteria

    public function __construct(?string $title = null, ?int $page = null, ?int $limit = null)
    {
        $this->title = $title;
        $this->page = $page ?: 1;
        $this->limit = $limit ?: 10;
    }
}
