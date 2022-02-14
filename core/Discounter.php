<?php

namespace core;

use interfaces\DiscounterInterface;

/**
 * Выполняет расчет скидки для товаров
 */
class Discounter implements DiscounterInterface
{
    /**
     * @var array Массив с конфигурацией
     */
    private array $config;

    /**
     * @var array Массив с информацией о скидках для товаров
     */
    private array $discounts;

    /**
     * @var array Массив продуктов
     */
    private array $products;

    /**
     * @var string Здесь хранятся продукты последовательно в виде строки - для определения скидки с помощью регулярного выражения
     */
    private string $productString = "";

    /**
     * В конструкторе можно установить начальный список продуктов
     * @param array $products Массив продуктов
     */
    public function __construct(array $products)
    {
        $this->products = $products;
        $this->config = require(dirname(__DIR__) . '/config/config.php');
    }

    public function setProducts(array $products): void
    {
        $this->products = $products;
    }

    // Метод запоминает данные о скидках в массиве $discounts (какому товару - какие скидки), затем проходит по массиву и применяет скидки
    public function applyDiscounts(): void
    {
        $this->discounts = [];
        $this->productString = CartManager::productsToString($this->products);

        // Если список товаров попадет под скидку, связанную с кол-вом товаров, в эти переменные попадает строка с товарами для скидки из конфига и размер скидки.
        // Это сделано для того, чтобы подобные скидки применялись в конце.
        $intRule = "";
        $intDiscount = "";

        foreach ($this->config['discounts'] as $discount => $rules) {
            foreach ($rules as $conditionRule => $applyRule) {
                // Если правило - строка, поставим перед каждой буквой ".*", так как товары необязательно должны быть рядом друг с другом для правила вида "AM"
                // Затем обработаем правило и запомним скидку для указанных товаров
                if (is_string($conditionRule)) {
                    $conditionRule = preg_replace("/([A-Z])/", "$0.*", $conditionRule);
                    if (preg_match("/$conditionRule/", $this->productString)) {
                        $this->processRule($applyRule, $discount);
                    }
                // Если правило - целое число, подразумевается кол-во товаров. Отложим данные о скидке в переменных и запомним скидку в конце.
                } elseif (is_int($conditionRule) && strlen($this->productString) == $conditionRule) {
                    $intRule = $applyRule;
                    $intDiscount = $discount;
                }

            }
        }

        // Отложенная скидка
        if (!empty($intRule) && !empty($intDiscount)) {
            $this->processRule($intRule, $intDiscount);
        }

        // Применяем скидки к товарам
        $this->apply();
    }

    /**
     * Определяет, каким товарам дается скидка и записывает кол-во скидок для каждого товара
     * @param string $applyRule Строка из конфига, содержащая информацию о том, каким товарам дается или не дается скидка
     * @param string $discount Размер скидки
     */
    private function processRule(string $applyRule, string $discount): void
    {
        $discounted = $this->getProductsForDiscount($applyRule);
        // В данный массив помещаются товары, к которым была применена скидка. Если, например, мы имеем товары D, E, D, E, и скидка за 4 товара - 10% на каждый, данный код не позволит дважды применить скидку к D и E
        $productsWithDiscount = [];

        foreach ($discounted as $item) {
            // Если можно применять скидку ко всем товарам, и данный товар еще не обрабатывался ранее этим правилом, либо же иначе - если скидка на товар еще не задана
            if (($this->config['multipleDiscounts'] && !in_array($item, $productsWithDiscount)) || !isset($this->discounts[$item])) {
                $productsWithDiscount[] = $item;
                $this->discounts[$item][] = $discount;
            }
        }
    }

    /**
     * Определяет, каким товарам дается скидка
     * @param string $applyRule Строка из конфига, содержащая информацию о том, каким товарам дается или не дается скидка
     * @return array Массив строк, хранящих названия товаров
     */
    private function getProductsForDiscount(string $applyRule): array
    {
        $processedApplyRule = $this->productString;
        // Если в строке с товарами для скидки указано исключение каких-либо товаров, просто уберем товары из строки всех товаров.
        preg_replace_callback(
            "/(!([A-Z]))/",
            function ($matches) use (&$processedApplyRule, &$applyRule) {
                $processedApplyRule = str_replace($matches[2], '', $processedApplyRule);
                $applyRule = str_replace($matches[1], '', $applyRule);
            },
            $applyRule
        );
        // И если после этого строка стала пустой, измененная строка всех товаров - товары со скидкой
        if (empty($applyRule)) {
            return str_split($processedApplyRule);
        }
        // А иначе просто используем товары, что указаны в строке товаров для скидки
        return str_split($applyRule);
    }

    /**
     * Применяет скидки для товаров
     */
    private function apply(): void
    {
        foreach ($this->products as $product) {
            $name = $product->getShortName();
            if (isset($this->discounts[$name])) {
                foreach ($this->discounts[$name] as $discount) {
                    $product->discount($discount);
                }
            }
        }
    }

}
