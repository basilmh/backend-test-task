<?php
/**
 * @author    Vasyl Minikh <mhbasil1@gmail.com>
 * @copyright 2024
 */
declare(strict_types=1);

namespace App\Controller\Oder;

use App\Requests\CalculatePriceRequest;
use App\Requests\PurchaseRequest;
use App\Utils\PaymentProcessor;
use App\Utils\PriceProcessor;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller used to calculate and purchase.
 */
final class ProductController extends AbstractController
{
    private PaymentProcessor $paymentProcessor;
    private PriceProcessor $priceProcessor;
    private RequestStack $requestStack;

    public function __construct(
        PaymentProcessor $paymentProcessor,
        PriceProcessor $priceProcessor,
        RequestStack $requestStack
    ) {
        $this->paymentProcessor = $paymentProcessor;
        $this->priceProcessor = $priceProcessor;
        $this->requestStack = $requestStack;
    }

    /**
     * Calculate Product price.
     * @throws Exception
     */
    public function calculate(CalculatePriceRequest $request): Response
    {
        if (!$this->isTokenValid('calculate')) {
            return $this->json([], Response::HTTP_UNAUTHORIZED);
        }

        $errors = $request->validate();

        $productId = $request->getProduct();
        $taxNumber = $request->getTaxNumber();
        $couponCode = $request->getCouponCode();

        $price = $this->priceProcessor->calculatePrice($productId, $taxNumber, $couponCode);

        if ($errors) {
            return $this->json([
                'product' => $productId,
                'taxNumber' => $taxNumber,
                'couponCode' => $couponCode,
                'messages' => $errors,
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->json([
            'product' => $productId,
            'taxNumber' => $taxNumber,
            'couponCode' => $couponCode,
            'price' => $price,
        ]);
    }

    /**
     * Purchase.
     * @throws Exception
     */
    public function purchase(PurchaseRequest $request): Response
    {
        if (!$this->isTokenValid('purchase')) {
            return $this->json([], Response::HTTP_UNAUTHORIZED);
        }

        $errors = $request->validate();

        $product = $request->getProduct();
        $taxNumber = $request->getTaxNumber();
        $couponCode = $request->getCouponCode();
        $paymentProcessor = $request->getPaymentProcessor();

        if ($errors) {
            return $this->json([
                'product' => $product,
                'taxNumber' => $taxNumber,
                'couponCode' => $couponCode,
                'paymentProcessor' => $paymentProcessor,
                'messages' => $errors,
            ], Response::HTTP_BAD_REQUEST);
        }

        $price = $this->priceProcessor->calculatePrice($product, $taxNumber, $couponCode);

        $resultPaymentError = $this->paymentProcessor->processPayment($price, $paymentProcessor);

        if ($resultPaymentError) {
            return $this->json([
                'product' => $product,
                'taxNumber' => $taxNumber,
                'couponCode' => $couponCode,
                'paymentProcessor' => $paymentProcessor,
                'messages' => $resultPaymentError,
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->json([
            'product' => $product,
            'taxNumber' => $taxNumber,
            'couponCode' => $couponCode,
            'paymentProcessor' => $paymentProcessor,
            'price' => $price,
            'messages' => ['message' => 'payment_was_done'],
        ]);
    }

    protected function isTokenValid(string $tokenId): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        /** @var string|null $token */
        $token = $request->get('_token');

        return $this->isCsrfTokenValid($tokenId, $token);
    }
}
