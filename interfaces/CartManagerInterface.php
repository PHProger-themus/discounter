<?php

namespace interfaces;

interface CartManagerInterface
{
    /**
     * Устанавливает список продуктов для класса работы с товарами
     * @param array $products Массив продуктов
     */
    public function setProducts(array $products): void;

    /**
     * Расчет итоговой суммы товаров
     * @return float Итоговая сумма
     */
    public function calculateTotal() : float;

    /**
     * "Конвертирует" массив продуктов в строку для обработки ее регулярным выражением
     * @param array $products Массив продуктов
     * @return string Строка
     */
    public static function productsToString(array $products): string;
}