<?php

namespace interfaces;

interface DiscounterInterface
{
    /**
     * Устанавливает список продуктов для класса работы со скидками
     * @param array $products Массив продуктов
     */
    public function setProducts(array $products): void;

    /**
     * Применяет скидки к продуктам
     */
    public function applyDiscounts(): void;
}