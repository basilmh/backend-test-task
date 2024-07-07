<?php
/**
 * @author    Vasyl Minikh <mhbasil1@gmail.com>
 * @copyright 2024
 */
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Tax;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TaxFixtures extends Fixture
{
    private const array TAXES = [
        0 => ['code' => '/^DE[0-9-]{9}+$/', 'name' => 'Германия', 'value' => 19],
        1 => ['code' => '/^IT[0-9-]{11}+$/', 'name' => 'Италия', 'value' => 22],
        2 => ['code' => '/^FR[a-zA-Z]{2}[0-9-]{9}+$/', 'name' => 'Франция', 'value' => 20],
        3 => ['code' => '/^GR[0-9-]{9}+$/', 'name' => 'Греция', 'value' => 24],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::TAXES as $tax) {
            $taxItem = new Tax();
            $taxItem->setCode($tax['code']);
            $taxItem->setName($tax['name']);
            $taxItem->setValue($tax['value']);
            $manager->persist($taxItem);
        }

        $manager->flush();
    }
}
