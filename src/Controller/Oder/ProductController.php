<?php
/**
 * @author    Vasyl Minikh <mhbasil1@gmail.com>
 * @copyright 2024
 */
declare(strict_types=1);

namespace App\Controller\Oder;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller used to calculate and purchase.
 */
final class ProductController extends AbstractController
{
    /**
     * Calculate Product price.
     */
    public function calculate(Request $request): Response
    {
        /** @var string|null $token */
        $token = $request->getPayload()->get('_token');

        if (!$this->isCsrfTokenValid('calculate', $token)) {
            return $this->json([], Response::HTTP_UNAUTHORIZED);
        }

        $product = $request->get('product');
        $taxNumber = $request->get('taxNumber');
        $couponCode = $request->get('couponCode');

        return $this->json([
            'product' => $product,
            'taxNumber' => $taxNumber,
            'couponCode' => $couponCode,
        ]);
    }

    /**
     * Purchase.
     */
    public function purchase(Request $request): Response
    {
        /** @var string|null $token */
        $token = $request->getPayload()->get('_token');

        if (!$this->isCsrfTokenValid('purchase', $token)) {
            return $this->json([], Response::HTTP_UNAUTHORIZED);
        }

        $product = $request->get('product');
        $taxNumber = $request->get('taxNumber');
        $couponCode = $request->get('couponCode');

        return $this->json([
            'product' => $product,
            'taxNumber' => $taxNumber,
            'couponCode' => $couponCode,
        ]);
    }
}
