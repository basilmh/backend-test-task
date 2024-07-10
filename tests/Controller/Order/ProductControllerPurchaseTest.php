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

final class ProductControllerPurchaseTest extends WebTestCase
{
    private const string URL = '/purchase';

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

    public function testWithToken(): void
    {
        $client = $this->createAuthenticatedApiClient('purchase');
        $client->request(
            'POST',
            self::URL,
            [],
            [],
            self::REQUEST_HEADERS,
            json_encode([
                'product' => 1,
                'taxNumber' => 'IT12345678900',
                'couponCode' => 'MINUS5',
                'paymentProcessor' => 'paypal',
                '_token' => $this->csrfToken,
            ])
        );

        $response = $client->getResponse();

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testWrongData(): void
    {
        $client = $this->createAuthenticatedApiClient('purchase');
        $client->request(
            'POST',
            self::URL,
            [],
            [],
            self::REQUEST_HEADERS,
            json_encode([
                'product' => 10,
                'taxNumber' => 'FAKETAX',
                'couponCode' => 'MINUS57',
                'paymentProcessor' => 'wrong',
                '_token' => $this->csrfToken,
            ])
        );

        $response = $client->getResponse();

        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertStringContainsString(
            'Coupon code is wrong',
            $response->getContent()
        );
        $this->assertStringContainsString(
            'Product id is wrong',
            $response->getContent()
        );
        $this->assertStringContainsString(
            'Tax FAKETAX does not has right format.',
            $response->getContent()
        );
        $this->assertStringContainsString(
            'The value you selected is not a valid choice.',
            $response->getContent()
        );
    }

    public function testRightData(): void
    {
        $client = $this->createAuthenticatedApiClient('purchase');
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
                'paymentProcessor' => 'paypal',
                '_token' => $this->csrfToken,
            ])
        );

        $response = $client->getResponse();

        $data = json_decode($response->getContent(), true);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('price', $data);
        $this->assertSame(11305, $data['price']); // (10000-500)(1+0.19) = 11305

        $this->assertStringContainsString(
            'payment_was_done',
            $response->getContent()
        );
    }
}
