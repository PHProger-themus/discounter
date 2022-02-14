<?php

namespace tests;

use core\CartManager;
use core\Discounter;
use PHPUnit\Framework\TestCase;

class Test extends TestCase
{
    private static Discounter $discounter;
    private static CartManager $cartManager;

    public static function setUpBeforeClass(): void
    {
        self::$cartManager = new CartManager([]);
        self::$discounter = new Discounter([]);
    }

    private function configureServices(array $products)
    {
        self::$cartManager->setProducts($products);
        self::$discounter->setProducts($products);
        self::$discounter->applyDiscounts();
    }

    public function testABandFourProducts()
    {
        $products = [
            new \products\A(),
            new \products\F(),
            new \products\B(),
            new \products\E(),
        ];
        $this->configureServices($products);

        $this->assertEquals(333, self::$cartManager->calculateTotal());
    }

    public function testABEMandFiveProducts()
    {
        $products = [
            new \products\A(),
            new \products\F(),
            new \products\B(),
            new \products\E(),
            new \products\M(),
        ];
        $this->configureServices($products);

        $this->assertEquals(339, self::$cartManager->calculateTotal());
    }

    public function testABandFiveProductsExcludeAandC()
    {
        $products = [
            new \products\A(),
            new \products\A(),
            new \products\B(),
            new \products\E(),
            new \products\C(),
        ];
        $this->configureServices($products);

        $this->assertEquals(293, self::$cartManager->calculateTotal());
    }

    public function testEFGandFiveProducts()
    {
        $products = [
            new \products\G(),
            new \products\E(),
            new \products\F(),
            new \products\M(),
            new \products\L(),
        ];
        $this->configureServices($products);

        $this->assertEquals(305.3, self::$cartManager->calculateTotal());
    }

    public function testELandThreeProducts()
    {
        $products = [
            new \products\E(),
            new \products\L(),
            new \products\K(),
        ];
        $this->configureServices($products);

        $this->assertEquals(209, self::$cartManager->calculateTotal());
    }

}
