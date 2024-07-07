<?php
/**
 * @author    Vasyl Minikh <mhbasil1@gmail.com>
 * @copyright 2024
 */
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    private const array PRODUCTS = [
        0 => ['name' => 'Iphone', 'price' => 10000],
        1 => ['name' => 'Наушники', 'price' => 2000],
        2 => ['name' => 'Чехол', 'price' => 1000],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::PRODUCTS as $product) {
            $productItem = new Product();
            $productItem->setName($product['name']);
            $productItem->setPrice($product['price']);
            $manager->persist($productItem);
        }

        $manager->flush();
    }
}
