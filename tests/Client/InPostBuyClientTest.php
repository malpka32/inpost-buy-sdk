<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests\Client;

use malpka32\InPostBuySdk\Auth\AccessTokenProviderInterface;
use malpka32\InPostBuySdk\Client\InPostBuyClient;
use malpka32\InPostBuySdk\Tests\Fixtures\ApiMocks;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class InPostBuyClientTest extends TestCase
{
    public function testCreateWithTokenProviderReturnsClient(): void
    {
        $tokenProvider = $this->createStub(AccessTokenProviderInterface::class);
        $tokenProvider->method('getAccessToken')->willReturn('test-bearer-token');

        $categoriesJson = json_encode(ApiMocks::categoriesResponseWithKey());
        $httpClient = $this->createHttpClientReturning(200, $categoriesJson);

        $client = InPostBuyClient::createWithTokenProvider($httpClient, $tokenProvider, 'org-123', true);

        $this->assertInstanceOf(InPostBuyClient::class, $client);
        $categories = $client->getCategories();
        $this->assertCount(2, $categories);
    }

    public function testConstructorWithCredentialsCreatesClientCredentialsProvider(): void
    {
        $categoriesJson = json_encode(ApiMocks::categoriesResponseWithKey());
        $tokenJson = json_encode(['access_token' => 'cc-token', 'expires_in' => 3600]);
        $httpClient = $this->createHttpClientWithTokenAndCategories($tokenJson, $categoriesJson);

        $client = new InPostBuyClient($httpClient, 'client-id', 'client-secret', 'org-123', true);

        $categories = $client->getCategories();
        $this->assertCount(2, $categories);
    }

    private function createHttpClientReturning(int $statusCode, string $body): HttpClientInterface
    {
        $response = $this->createStub(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn($statusCode);
        $response->method('getContent')->with(false)->willReturn($body);
        $response->method('getHeaders')->with(false)->willReturn([]);

        $client = $this->createMock(HttpClientInterface::class);
        $client->method('request')->willReturn($response);

        return $client;
    }

    private function createHttpClientWithTokenAndCategories(string $tokenResponse, string $categoriesResponse): HttpClientInterface
    {
        $tokenResp = $this->createStub(ResponseInterface::class);
        $tokenResp->method('getStatusCode')->willReturn(200);
        $tokenResp->method('getContent')->with(false)->willReturn($tokenResponse);

        $categoriesResp = $this->createStub(ResponseInterface::class);
        $categoriesResp->method('getStatusCode')->willReturn(200);
        $categoriesResp->method('getContent')->with(false)->willReturn($categoriesResponse);
        $categoriesResp->method('getHeaders')->with(false)->willReturn([]);

        $client = $this->createMock(HttpClientInterface::class);
        $client->method('request')->willReturnCallback(function (string $method, string $url, array $options = []) use ($tokenResp, $categoriesResp) {
            if (str_contains($url, '/oauth2/token')) {
                return $tokenResp;
            }

            return $categoriesResp;
        });

        return $client;
    }
}
