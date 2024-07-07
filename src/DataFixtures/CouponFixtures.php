<?php
/**
 * @author    Vasyl Minikh <mhbasil1@gmail.com>
 * @copyright 2024
 */
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Coupon;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CouponFixtures extends Fixture
{
    private const array COUPONS = [
        0 => ['code' => 'MINUS10%', 'value' => 1000, 'type' => true],
        1 => ['code' => 'MINUS5', 'value' => 500, 'type' => false],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::COUPONS as $coupon) {
            $couponItem = new Coupon();
            $couponItem->setCode($coupon['code']);
            $couponItem->setValue($coupon['value']);
            $couponItem->setType($coupon['type']);
            $manager->persist($couponItem);
        }

        $manager->flush();
    }
}
