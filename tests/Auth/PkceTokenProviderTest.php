<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests\Auth;

use malpka32\InPostBuySdk\Auth\PkceOAuth2Client;
use malpka32\InPostBuySdk\Auth\PkceTokenProvider;
use malpka32\InPostBuySdk\Auth\TokenStorageInterface;
use malpka32\InPostBuySdk\Exception\ApiException;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class PkceTokenProviderTest extends TestCase
{
    public function testGetAccessTokenReturnsStoredTokenWhenNotExpired(): void
    {
        $storage = $this->createTokenStorageWithData('valid-token', 'refresh', time() + 300);
        $pkceClient = new PkceOAuth2Client($this->createStub(HttpClientInterface::class));
        $client = new PkceTokenProvider($storage, $pkceClient, '', '', '', 60);

        $token = $client->getAccessToken();

        $this->assertSame('valid-token', $token);
    }

    public function testGetAccessTokenRefreshesWhenExpired(): void
    {
        $storage = $this->createTokenStorageWithData('old-token', 'refresh-token', time() - 10);
        $tokenResponse = $this->createTokenResponse(['access_token' => 'new-at', 'refresh_token' => 'new-rt', 'expires_in' => 3600]);
        $httpClient = $this->createHttpClientThatReturns($tokenResponse);
        $pkceClient = new PkceOAuth2Client($httpClient);
        $provider = new PkceTokenProvider($storage, $pkceClient, 'cid', 'secret', 'https://token.example', 60);

        $token = $provider->getAccessToken();

        $this->assertSame('new-at', $token);
        $this->assertSame('new-at', $storage->getAccessToken());
    }

    public function testGetAccessTokenThrowsWhenNoRefreshToken(): void
    {
        $storage = $this->createTokenStorageWithData('old', null, time() - 10);
        $pkceClient = new PkceOAuth2Client($this->createStub(HttpClientInterface::class));
        $provider = new PkceTokenProvider($storage, $pkceClient, '', '', '', 60);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('No refresh token');

        $provider->getAccessToken();
    }

    private function createTokenStorageWithData(string $accessToken, ?string $refreshToken, int $expiresAt): TokenStorageInterface
    {
        return new class ($accessToken, $refreshToken, $expiresAt) implements TokenStorageInterface {
            private ?string $accessToken;
            private ?string $refreshToken;
            private ?int $expiresAt;

            public function __construct(?string $accessToken, ?string $refreshToken, ?int $expiresAt)
            {
                $this->accessToken = $accessToken;
                $this->refreshToken = $refreshToken;
                $this->expiresAt = $expiresAt;
            }

            public function setTokens(string $accessToken, string $refreshToken, int $expiresAt): void
            {
                $this->accessToken = $accessToken;
                $this->refreshToken = $refreshToken;
                $this->expiresAt = $expiresAt;
            }

            public function getAccessToken(): ?string
            {
                return $this->accessToken;
            }

            public function getRefreshToken(): ?string
            {
                return $this->refreshToken;
            }

            public function getExpiresAt(): ?int
            {
                return $this->expiresAt;
            }
        };
    }

    /**
     * @param array<string, mixed> $data
     */
    private function createTokenResponse(array $data): ResponseInterface
    {
        $response = $this->createStub(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getContent')->with(false)->willReturn(json_encode($data));

        return $response;
    }

    private function createHttpClientThatReturns(ResponseInterface $response): HttpClientInterface
    {
        $client = $this->createStub(HttpClientInterface::class);
        $client->method('request')->willReturn($response);

        return $client;
    }
}
