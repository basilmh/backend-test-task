<?php
/**
 * @author    Vasyl Minikh <mhbasil1@gmail.com>
 * @copyright 2024
 */
declare(strict_types=1);

namespace App\Payment;

use AllowDynamicProperties;
use Exception;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

#[AllowDynamicProperties]
class PaymentManager
{
    private array $availablePaymentServices = [];
    public function __construct(
        #[TaggedIterator('app.payment_services')]
        iterable $paymentServices
    ) {
        $this->paymentServices = $paymentServices;
    }

    /**
     * @throws Exception
     */
    public function getProvider(string $name): PaymentInterface
    {
        foreach ($this->paymentServices as $service) {
            if ($service instanceof $name) {
                return $service;
            }
        }
        throw new Exception("Payment service $name not found");
    }

    public function setAvailablePaymentServices(array $availablePaymentServices): void
    {
        $this->availablePaymentServices = $availablePaymentServices;
    }
    public function getAvailablePaymentServices(): array
    {
        return $this->availablePaymentServices;
    }

    /**
     * @throws Exception
     */
    public function getPaymentServices(string $paymentName): PaymentInterface
    {
        $paymentProcessors = $this->getAvailablePaymentServices();
        $paymentProcessorName = $paymentProcessors[$paymentName] ?? '';

        return $this->getProvider($paymentProcessorName);
    }
}
