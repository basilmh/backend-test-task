<?php
/**
 * @author    Vasyl Minikh <mhbasil1@gmail.com>
 * @copyright 2024
 */
declare(strict_types=1);

namespace App\Payment;

interface PaymentInterface
{
    public function makePayment(int $price): void;

    public function getClient(): string;
}
