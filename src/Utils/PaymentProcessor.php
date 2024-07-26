<?php
/**
 * @author    Vasyl Minikh <mhbasil1@gmail.com>
 * @copyright 2024
 */
declare(strict_types=1);

namespace App\Utils;

use App\Payment\PaymentManager;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class PaymentProcessor
{
    public function __construct(
        private ContainerBagInterface $params,
        private PaymentManager $paymentManager
    ) {
    }

    public function processPayment(int $price, string $paymentName): array
    {
        $message = [];

        try {
            $paymentProcessors = $this->params->get('app.payments');
            $paymentProcessorName = $paymentProcessors[$paymentName] ?? '';
            $paymentProcessor = $this->paymentManager->getProvider($paymentProcessorName);

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
