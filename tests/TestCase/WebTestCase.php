<?php
/**
 * @author    Vasyl Minikh <mhbasil1@gmail.com>
 * @copyright 2024
 *
 */
declare(strict_types=1);

namespace App\Tests\TestCase;

use App\Tests\SessionHelper;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

class WebTestCase extends BaseWebTestCase
{
    use SessionHelper;

    protected ?string $csrfToken = null;

    protected function createAnonymousApiClient(): KernelBrowser
    {
        return static::createClient([], [
            'CONTENT_TYPE' => 'application/json',
        ]);
    }

    protected function createAuthenticatedApiClient(string $tokenId = 'calculate'): KernelBrowser
    {
        $client = static::createClient([], [
            'CONTENT_TYPE' => 'application/json'
        ]);
        $csrfToken = $this->generateCsrfToken($client, $tokenId);
        $this->csrfToken = $csrfToken;

        return $client;
    }
}