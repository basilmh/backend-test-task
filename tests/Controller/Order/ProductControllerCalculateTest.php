<?php
/**
 * @author    Vasyl Minikh <mhbasil1@gmail.com>
 * @copyright 2024
 */
declare(strict_types=1);

namespace App\Tests\Controller\Order;

use App\Tests\TestCase\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

use function json_decode;
use function json_encode;

final class ProductControllerCalculateTest extends WebTestCase
{
    private const string URL = '/calculate-price';

    public function testAsAnonymous(): void
    {
        $client = $this->createAnonymousApiClient();

        $client->request(
            'POST',
            self::URL,
            [],
            [],
            self::REQUEST_HEADERS,
            json_encode([
                'product' => 1,
                'taxNumber' => 'DE123456789',
                'couponCode' => 'MINUS5',
            ])
        );

        $response = $client->getResponse();

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testCalculate(): void
    {
        $client = $this->createAuthenticatedApiClient();
        $client->request(
            'POST',
            self::URL,
            [],
            [],
            self::REQUEST_HEADERS,
            json_encode([
                'product' => 1,
                'taxNumber' => 'DE123456789',
                'couponCode' => 'MINUS5',

                '_token' => $this->csrfToken,
            ])
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('price', $data);
        /* Logic: (10000-500)(1+0.19) = 11305 */
        $this->assertSame(11305, $data['price']);
    }

    public function testWrongTaxNumber(): void
    {
        $client = $this->createAuthenticatedApiClient();
        $client->request(
            'POST',
            self::URL,
            [],
            [],
            self::REQUEST_HEADERS,
            json_encode([
                'product' => 1,
                'taxNumber' => 'DE1234567',
                'couponCode' => 'MINUS5',

                '_token' => $this->csrfToken,
            ])
        );

        $response = $client->getResponse();

        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertStringContainsString(
            'Tax DE1234567 does not has right format.',
            $response->getContent()
        );
    }

    public function testWrongCouponNumber(): void
    {
        $client = $this->createAuthenticatedApiClient();
        $client->request(
            'POST',
            self::URL,
            [],
            [],
            self::REQUEST_HEADERS,
            json_encode([
                'product' => 1,
                'taxNumber' => 'DE123456789',
                'couponCode' => 'MINUS51',

                '_token' => $this->csrfToken,
            ])
        );

        $response = $client->getResponse();

        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertStringContainsString(
            'Coupon code is wrong',
            $response->getContent()
        );
    }

    public function testWrongProduct(): void
    {
        $client = $this->createAuthenticatedApiClient();
        $client->request(
            'POST',
            self::URL,
            [],
            [],
            self::REQUEST_HEADERS,
            json_encode([
                'product' => 10,
                'taxNumber' => 'DE123456789',
                'couponCode' => 'MINUS5',

                '_token' => $this->csrfToken,
            ])
        );

        $response = $client->getResponse();

        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertStringContainsString(
            'Product id is wrong',
            $response->getContent()
        );
    }
}
