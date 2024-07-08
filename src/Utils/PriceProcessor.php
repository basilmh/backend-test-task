<?php
/**
 * @author    Vasyl Minikh <mhbasil1@gmail.com>
 * @copyright 2024
 */
declare(strict_types=1);

namespace App\Utils;

use App\Entity\Coupon;
use App\Entity\Tax;
use App\Repository\CouponRepository;
use App\Repository\ProductRepository;
use App\Repository\TaxRepository;
use Exception;

use function round;

class PriceProcessor
{
    private TaxRepository $taxRepository;
    private CouponRepository $couponRepository;
    private ProductRepository $productRepository;

    public function __construct(
        TaxRepository $taxRepository,
        CouponRepository $couponRepository,
        ProductRepository $productRepository
    ) {
        $this->taxRepository = $taxRepository;
        $this->couponRepository = $couponRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @throws Exception
     */
    public function calculatePrice(int $productId, string $taxNumber, ?string $couponCode): int
    {
        $product = $this->productRepository->find($productId);
        $tax = $this->taxRepository->findByTaxPrefix($taxNumber);
        $coupon = $this->couponRepository->findOneByCouponCode($couponCode);

        if (null === $product || null === $tax) {
            throw new Exception('Something went wrong');
        }

        $price = $product->getPrice();

        $price = $this->priceWithCoupon($coupon, $price);

        return $this->priceWithTax($tax, $price);
    }

    private function priceWithCoupon(?Coupon $coupon, $price): int
    {
        if (null === $coupon) {
            return $price;
        }
        if ($coupon->isType()) { // percent discount
            $price = (int) round($price * (1 - $coupon->getValue() / 100));
            if ($price < 0) {
                return 0;
            }

            return $price;
        }

        $price = $price - $coupon->getValue();

        if ($price < 0) {
            return 0;
        }

        return $price;
    }

    private function priceWithTax(Tax $tax, $price): int
    {
        return (int) round($price * (1 + $tax->getValue() / 100));
    }
}
