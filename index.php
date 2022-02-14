<?php
// TODO: tests
require_once('vendor/autoload.php');

$products = [
    new \products\A(),
    new \products\F(),
    new \products\B(),
    new \products\E(),
    new \products\M(),
];
$cartManager = new \core\CartManager($products);
$total = $cartManager->calculateTotal();

echo "Products before discount was applied:<br />";
foreach ($products as $product) {
    echo "{$product->getName()} costs {$product->getPrice()} <br />";
}
echo "Total: $total <br />";

$discounter = new \core\Discounter($products);
$discounter->applyDiscounts();

$newTotal = $cartManager->calculateTotal();
$discount = round(100 - $newTotal * 100 / $total);
echo "<br />Products after discount was applied:<br />";
foreach ($products as $product) {
    echo "{$product->getName()} costs {$product->getPrice()} <br />";
}
echo "Total: $newTotal (Discount: $discount%)<br />";
