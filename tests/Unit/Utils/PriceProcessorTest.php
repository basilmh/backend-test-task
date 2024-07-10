<?php
/**
 * @author    Vasyl Minikh <mhbasil1@gmail.com>
 * @copyright 2024
 */
declare(strict_types=1);

namespace App\Tests\Unit\Utils;

use App\Entity\Coupon;
use App\Entity\Product;
use App\Entity\Tax;
use App\Repository\CouponRepository;
use App\Repository\ProductRepository;
use App\Repository\TaxRepository;
use App\Utils\PriceProcessor;
use Exception;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
class PriceProcessorTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCalculatePrice(): void
    {
        $tax = new Tax();
        $tax->setValue(19);
        $tax->setCode('/^DE[0-9-]{9}+$/');
        $tax->setName('Германия');

        $taxRepository = $this->getMockBuilder(TaxRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $taxRepository->method('findByTaxPrefix')->willReturn($tax);

        $coupon = new Coupon();
        $coupon->setValue(500);
        $coupon->setCode('MINUS5');
        $coupon->setType(false);

        $couponRepository = $this->getMockBuilder(CouponRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $couponRepository->method('findOneByCouponCode')->willReturn($coupon);

        $product = new Product();
        $product->setPrice(10000);
        $product->setName('Iphone');

        $productRepository = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $productRepository->method('find')->willReturn($product);
        $priceProcessor = $this->getMockBuilder(PriceProcessor::class)
            ->setConstructorArgs(
                [
                    'taxRepository' => $taxRepository,
                    'couponRepository' => $couponRepository,
                    'productRepository' => $productRepository,
                ]
            )
            ->onlyMethods([])
            ->getMock();

        $calculatedPrice = $priceProcessor->calculatePrice(1, 'DE123456789', 'MINUS5');

        self::assertSame(11305, $calculatedPrice);
    }
}
