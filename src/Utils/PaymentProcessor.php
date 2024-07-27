<?php
/**
 * @author    Vasyl Minikh <mhbasil1@gmail.com>
 * @copyright 2024
 */
declare(strict_types=1);

namespace App\Utils;

use App\Payment\PaymentManager;
use Exception;

class PaymentProcessor
{
    public function __construct(
        private PaymentManager $paymentManager
    ) {
    }

    public function processPayment(int $price, string $paymentName): array
    {
        $message = [];

        try {
            $paymentProcessor = $this->paymentManager->getPaymentServices($paymentName);

            $paymentProcessor->makePayment($price);
        } catch (Exception $exception) {
            $message = [
                'message' => 'payment_failed',
                'errors' => ['value' => $price, 'message' => $exception->getMessage()],
            ];
        }

        return $message;
    }
}
