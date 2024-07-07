<?php
/**
 * @author    Vasyl Minikh <mhbasil1@gmail.com>
 * @copyright 2024
 */
declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;

trait SessionHelper
{
    public function getSession(KernelBrowser $client): Session
    {
        $cookie = $client->getCookieJar()->get('MOCKSESSID');

        /** @var Session $session */
        $session = static::getContainer()->get('session.factory')->createSession();

        if ($cookie) {
            // get the session id from the session cookie if it exists
            $session->setId($cookie->getValue());
            $session->start();

            return $session;
        }
        // or create a new session id and a session cookie
        $session->start();
        $session->save();

        $sessionCookie = new Cookie(
            $session->getName(),
            $session->getId(),
            null,
            null,
            'localhost',
        );
        $client->getCookieJar()->set($sessionCookie);

        return $session;
    }

    public function generateCsrfToken(KernelBrowser $client, string $tokenId): string
    {
        $session = $this->getSession($client);

        /** @var UriSafeTokenGenerator $tokenGenerator */
        $tokenGenerator = static::getContainer()->get('security.csrf.token_generator');

        $csrfToken = $tokenGenerator->generateToken();

        $session->set(SessionTokenStorage::SESSION_NAMESPACE . "/{$tokenId}", $csrfToken);
        $session->save();

        return $csrfToken;
    }
}
