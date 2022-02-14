<?php

namespace core;

/**
 * Класс для работы с товарами
 */
class CartManager
{
    /**
     * @var array Массив продуктов
     */
    private array $products;

    public function __construct(array $products)
    {
        $this->products = $products;
    }

    public function setProducts(array $products): void
    {
        $this->products = $products;
    }

    public function calculateTotal() : float
    {
        $total = 0;
        foreach ($this->products as $product) {
            $total += $product->getPrice();
        }
        return $total;
    }

    public static function productsToString(array $products): string
    {
        $productsAsChars = [];
        foreach ($products as $product) {
            $productsAsChars[] = $product->getShortName($product);
        }
        sort($productsAsChars);
        return implode('', $productsAsChars);
    }
}