<?php
/**
 * @author    Vasyl Minikh <mhbasil1@gmail.com>
 * @copyright 2024
 */
declare(strict_types=1);

namespace App\Tests\Controller\Order;

use App\Tests\TestCase\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ProductControllerPurchaseTest extends WebTestCase
{
    private const string URL = '/purchase';

    public function testAsAnonymous(): void
    {
        $client = $this->createAnonymousApiClient();
        $client->request('POST', self::URL);

        $response = $client->getResponse();
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testWithToken(): void
    {
        $client = $this->createAuthenticatedApiClient('purchase');
        $client->request(
            'POST',
            self::URL,
            [
                'product' => 1,
                'taxNumber' => 'IT12345678900',
                'couponCode' => 'D15',
                'paymentProcessor' => 'paypal',
                '_token' => $this->csrfToken,
            ]
        );

        $response = $client->getResponse();

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }
}
