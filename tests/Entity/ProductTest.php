<?php
namespace App\Tests\Entity;
use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('pricesForFoodProduct')]

    // ou @dataProvider methodName
    public function testComputeTVAFoodProduct($price, $expectedTVA)
    {
        // TVA 5.5% pour les produits alimentaires
        $product = new Product('Un produit', Product::FOOD_PRODUCT, $price);
        $this->assertSame($expectedTVA, $product->computeTVA());
    }


    // static is required for data providers
    public static function pricesForFoodProduct(): array
    {
        return [
            'prix zéro' => [0, 0.0],
            'prix vingt' => [20, 1.1],
            'prix cent' => [100, 5.5]
        ];
    }

    public function testComputeTVAOtherProduct()
    {
        // TVA 20% pour les autres produits
        $product = new Product('Un autre produit', 'Un autre type de produit', 20);
        $this->assertSame(3.92, $product->computeTVA());
    }

    public function testNegativePriceComputeTVA()
    {
        $product = new Product('Un produit', Product::FOOD_PRODUCT, -20);
        $this->expectException(\Exception::class);
        $product->computeTVA();
    }
}
