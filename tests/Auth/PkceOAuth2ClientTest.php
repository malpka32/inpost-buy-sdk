<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests\Auth;

use malpka32\InPostBuySdk\Auth\PkceOAuth2Client;
use malpka32\InPostBuySdk\Auth\PkceStateStorageInterface;
use malpka32\InPostBuySdk\Auth\TokenStorageInterface;
use malpka32\InPostBuySdk\Exception\ApiException;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class PkceOAuth2ClientTest extends TestCase
{
    public function testInitiateAuthorizationReturnsAuthorizeUrlAndState(): void
    {
        $storage = $this->createStateStorage();
        $httpClient = $this->createStub(HttpClientInterface::class);
        $client = new PkceOAuth2Client($httpClient);

        $result = $client->initiateAuthorization(
            'https://example.com/callback',
            'client-123',
            true,
            $storage,
        );

        $this->assertArrayHasKey('authorize_url', $result);
        $this->assertArrayHasKey('state', $result);
        $this->assertStringContainsString('https://stage-account.inpost-group.com/oauth2/authorize', $result['authorize_url']);
        $this->assertStringContainsString('response_type=code', $result['authorize_url']);
        $this->assertStringContainsString('client_id=client-123', $result['authorize_url']);
        $this->assertStringContainsString('redirect_uri=', $result['authorize_url']);
        $this->assertStringContainsString('code_challenge=', $result['authorize_url']);
        $this->assertStringContainsString('code_challenge_method=S256', $result['authorize_url']);
        $this->assertSame(32, strlen($result['state']));
    }

    public function testInitiateAuthorizationWithCustomScopes(): void
    {
        $storage = $this->createStateStorage();
        $httpClient = $this->createStub(HttpClientInterface::class);
        $client = new PkceOAuth2Client($httpClient);

        $result = $client->initiateAuthorization(
            'https://example.com/cb',
            'cid',
            false,
            $storage,
            null,
            'api:offers:read api:orders:read',
        );

        $this->assertStringContainsString('scope=api%3Aoffers%3Aread+api%3Aorders%3Aread', $result['authorize_url']);
    }

    public function testInitiateAuthorizationRespectsOverride(): void
    {
        $storage = $this->createStateStorage();
        $httpClient = $this->createStub(HttpClientInterface::class);
        $client = new PkceOAuth2Client($httpClient);

        $result = $client->initiateAuthorization(
            'https://example.com/cb',
            'cid',
            false,
            $storage,
            'https://custom.auth.example/authorize',
        );

        $this->assertStringContainsString('https://custom.auth.example/authorize', $result['authorize_url']);
    }

    public function testExchangeCodeForTokensStoresTokens(): void
    {
        $stateStorage = $this->createStateStorageWithData('state-xyz', 'verifier-abc');
        $tokenStorage = $this->createTokenStorage();
        $tokenResponse = $this->createTokenResponse([
            'access_token' => 'at-123',
            'refresh_token' => 'rt-456',
            'expires_in' => 3600,
        ]);
        $httpClient = $this->createHttpClientThatReturns($tokenResponse);
        $client = new PkceOAuth2Client($httpClient);

        $result = $client->exchangeCodeForTokens(
            'auth-code-789',
            'https://example.com/callback',
            'client-id',
            'client-secret',
            'state-xyz',
            'https://token.example/token',
            $stateStorage,
            $tokenStorage,
        );

        $this->assertSame('at-123', $result['access_token']);
        $this->assertSame('rt-456', $result['refresh_token']);
        $this->assertSame(3600, $result['expires_in']);
        $this->assertSame('at-123', $tokenStorage->getAccessToken());
        $this->assertSame('rt-456', $tokenStorage->getRefreshToken());
    }

    public function testExchangeCodeForTokensThrowsOnInvalidState(): void
    {
        $stateStorage = $this->createStateStorageWithData('stored-state', 'verifier');
        $tokenStorage = $this->createTokenStorage();
        $httpClient = $this->createStub(HttpClientInterface::class);

        $client = new PkceOAuth2Client($httpClient);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Invalid or missing PKCE state');

        $client->exchangeCodeForTokens(
            'code',
            'https://example.com/cb',
            'cid',
            'secret',
            'wrong-state',
            'https://token.example/token',
            $stateStorage,
            $tokenStorage,
        );
    }

    public function testRefreshAccessTokenReturnsNewToken(): void
    {
        $tokenStorage = $this->createTokenStorage();
        $tokenResponse = $this->createTokenResponse([
            'access_token' => 'new-at',
            'refresh_token' => 'new-rt',
            'expires_in' => 1800,
        ]);
        $httpClient = $this->createHttpClientThatReturns($tokenResponse);
        $client = new PkceOAuth2Client($httpClient);

        $token = $client->refreshAccessToken(
            'old-refresh-token',
            'client-id',
            'client-secret',
            'https://token.example/token',
            $tokenStorage,
        );

        $this->assertSame('new-at', $token);
        $this->assertSame('new-at', $tokenStorage->getAccessToken());
        $this->assertSame('new-rt', $tokenStorage->getRefreshToken());
    }

    private function createStateStorage(): PkceStateStorageInterface
    {
        return new class () implements PkceStateStorageInterface {
            private ?string $state = null;
            private ?string $codeVerifier = null;

            public function setState(string $state, string $codeVerifier): void
            {
                $this->state = $state;
                $this->codeVerifier = $codeVerifier;
            }

            public function getState(): ?string
            {
                return $this->state;
            }

            public function getCodeVerifier(): ?string
            {
                return $this->codeVerifier;
            }

            public function deleteState(): void
            {
                $this->state = null;
                $this->codeVerifier = null;
            }
        };
    }

    private function createStateStorageWithData(string $state, string $codeVerifier): PkceStateStorageInterface
    {
        return new class ($state, $codeVerifier) implements PkceStateStorageInterface {
            public function __construct(
                private readonly string $state,
                private readonly string $codeVerifier,
            ) {
            }

            public function setState(string $state, string $codeVerifier): void
            {
            }

            public function getState(): ?string
            {
                return $this->state;
            }

            public function getCodeVerifier(): ?string
            {
                return $this->codeVerifier;
            }

            public function deleteState(): void
            {
            }
        };
    }

    private function createTokenStorage(): TokenStorageInterface
    {
        return new class () implements TokenStorageInterface {
            private ?string $accessToken = null;
            private ?string $refreshToken = null;
            private ?int $expiresAt = null;

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
        $httpClient = $this->createStub(HttpClientInterface::class);
        $httpClient->method('request')->willReturn($response);

        return $httpClient;
    }
}
