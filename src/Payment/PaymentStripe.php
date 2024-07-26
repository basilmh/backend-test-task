<?php
/**
 * @author    Vasyl Minikh <mhbasil1@gmail.com>
 * @copyright 2024
 */
declare(strict_types=1);

namespace App\Payment;

use Exception;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

use function get_class;

/**
 * Make stripe payment.
 */
class PaymentStripe implements PaymentInterface
{
    public function __construct(private StripePaymentProcessor $stripePaymentProcessor)
    {
    }

    /**
     * @throws Exception
     */
    public function makePayment(int $price): void
    {
        if (!$this->stripePaymentProcessor->processPayment((float) $price / 10)) {
            throw new Exception('There was an error during payment');
        }
    }

    public function getClient(): string
    {
        return get_class($this);
    }
}
