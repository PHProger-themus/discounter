<?php

namespace interfaces;

interface ProductInterface
{
    /**
     * Применяет заданную скидку
     * @param int $discount Скидка (в процентах)
     */
    public function discount(int $discount): void;

    /**
     * Возвращает наименование товара
     * @return string Наименование товара
     */
    public function getName(): string;

    /**
     * Возвращает стоимость товара
     * @return float Стоимость товара
     */
    public function getPrice(): float;

    /**
     * Возвращает имя класса без неймспейса
     * @return string Имя класса
     */
    public function getShortName(): string;
}