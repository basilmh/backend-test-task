<?php
/**
 * @author    Vasyl Minikh <mhbasil1@gmail.com>
 * @copyright 2024
 */
declare(strict_types=1);

namespace App\Payment;

use Exception;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;
use function get_class;

/**
 * Make paypal payment.
 */
class PaymentPaypal implements PaymentInterface
{
    public function __construct(private PaypalPaymentProcessor $paymentProcessor)
    {
    }

    /**
     * @throws Exception
     */
    public function makePayment(int $price): void
    {
        $this->paymentProcessor->pay($price);
    }

    public function getClient(): string
    {
        return get_class($this);
    }
}
