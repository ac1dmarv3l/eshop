<?php

declare(strict_types=1);

namespace App\Product\Domain;

use Doctrine\ORM\Mapping as ORM;

/** @final */
#[ORM\Entity]
#[ORM\Table(name: 'product_products')]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'name', type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    private ?string $description;

    #[ORM\Column(name: 'price', type: 'string')]
    private string $price;

    #[ORM\Column(name: 'image_url', type: 'string', length: 500, nullable: true)]
    private ?string $imageUrl;

    private function __construct(
        string $name,
        string $description,
        string $price,
        string $imageUrl
    )
    {
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->imageUrl = $imageUrl;
    }

    public static function create(
        string $name,
        string $description,
        string $price,
        string $imageUrl
    ): self
    {
        return new self(
            $name,
            $description,
            $price,
            $imageUrl,
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }
}
