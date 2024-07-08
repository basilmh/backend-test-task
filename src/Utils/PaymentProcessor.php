<?php
/**
 * @author    Vasyl Minikh <mhbasil1@gmail.com>
 * @copyright 2024
 */
declare(strict_types=1);

namespace App\Utils;

use Exception;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

class PaymentProcessor
{
    private StripePaymentProcessor $stripePaymentProcessor;
    private PaypalPaymentProcessor $paypalPaymentProcessor;

    public function __construct(
        StripePaymentProcessor $stripePaymentProcessor,
        PaypalPaymentProcessor $paypalPaymentProcessor
    ) {
        $this->stripePaymentProcessor = $stripePaymentProcessor;
        $this->paypalPaymentProcessor = $paypalPaymentProcessor;
    }

    public function processPayment(int $price, string $paymentProcessor): array
    {
        $message = [];

        try {
            switch ($paymentProcessor) {
                case 'stripe':
                    $this->paypalPaymentProcessor->pay($price);
                    break;
                case 'paypal':
                    if (!$this->stripePaymentProcessor->processPayment((float) $price / 10)) {
                        throw new Exception('There was an error during payment');
                    }
                    break;
                default:
                    throw new Exception('Payment processor is not supported');
            }
        } catch (Exception $exception) {
            $message = [
                'message' => 'payment_failed',
                'errors' => ['value' => $price, 'message' => $exception->getMessage()],
            ];
        }
        return $message;
    }
}
