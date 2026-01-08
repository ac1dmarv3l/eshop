<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Repository;

use App\Product\Domain\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class ProductRepository
{
    private EntityRepository $entityRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
    )
    {
        $this->entityRepository = $entityManager->getRepository(Product::class);
    }

    public function findAll(): array
    {
        return $this->entityRepository->findAll();
    }

    public function findOneById(int $id): ?Product
    {
        return $this->entityRepository->find($id);
    }
}
