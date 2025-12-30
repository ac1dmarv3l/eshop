<?php

declare(strict_types=1);

namespace App\Service;

final class ProductsProviderService
{
    private array $products;

    private static array $productsCache = [];

    public function __construct()
    {
        if (empty(self::$productsCache)) {
            self::$productsCache = require __DIR__ . '/../config/products.php';
        }
        $this->products = self::$productsCache;
    }

    public function getProducts(): array
    {
        return $this->products;
    }
}
