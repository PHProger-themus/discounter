<?php

namespace core;

use interfaces\ProductInterface;

/**
 * Базовый класс продукта
 */
class Product implements ProductInterface
{
    /**
     * @var string Наименование товара
     */
    protected string $name;

    /**
     * @var float Стоимость товара
     */
    protected float $price;

    public function discount(int $discount): void
    {
        $this->price = round($this->price * ((100 - $discount) / 100), 2);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getShortName(): string
    {
        return (new \ReflectionClass($this))->getShortName();
    }
}
