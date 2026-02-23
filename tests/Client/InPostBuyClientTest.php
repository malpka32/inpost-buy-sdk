<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests\Client;

use malpka32\InPostBuySdk\Auth\AccessTokenProviderInterface;
use malpka32\InPostBuySdk\Client\InPostBuyClient;
use malpka32\InPostBuySdk\Config\Language;
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

        $categoriesJson = json_encode(ApiMocks::categoriesTreeResponse());
        $httpClient = $this->createHttpClientReturning(200, $categoriesJson);

        $client = InPostBuyClient::createWithTokenProvider($httpClient, $tokenProvider, 'org-123', true);

        $this->assertInstanceOf(InPostBuyClient::class, $client);
        $categories = $client->getCategories();
        $this->assertCount(1, $categories);
        $this->assertSame('root-uuid', $categories->offsetGet(0)->id);
    }

    public function testGetOfferAttachmentsReturnsCollection(): void
    {
        $tokenProvider = $this->createStub(AccessTokenProviderInterface::class);
        $tokenProvider->method('getAccessToken')->willReturn('test-token');

        $attachmentsJson = json_encode(ApiMocks::attachmentsListResponse());
        $attachmentsResponse = $this->createStub(ResponseInterface::class);
        $attachmentsResponse->method('getStatusCode')->willReturn(200);
        $attachmentsResponse->method('getContent')->with(false)->willReturn($attachmentsJson);
        $attachmentsResponse->method('getHeaders')->with(false)->willReturn([]);

        $client = $this->createMock(HttpClientInterface::class);
        $client->method('request')->willReturnCallback(function (string $method, string $url) use ($attachmentsResponse) {
            if (str_contains($url, '/attachments') && !str_contains($url, '/attachments/')) {
                return $attachmentsResponse;
            }
            $categoriesJson = json_encode(ApiMocks::categoriesTreeResponse());
            $r = $this->createStub(ResponseInterface::class);
            $r->method('getStatusCode')->willReturn(200);
            $r->method('getContent')->with(false)->willReturn($categoriesJson);
            $r->method('getHeaders')->with(false)->willReturn([]);
            return $r;
        });

        $client = InPostBuyClient::createWithTokenProvider($client, $tokenProvider, 'org-123', true);

        $attachments = $client->getOfferAttachments('offer-1');
        $this->assertCount(1, $attachments);
        $first = $attachments->first();
        $this->assertNotNull($first);
        $this->assertSame('att-uuid-123', $first->id);
    }

    public function testConstructorWithCredentialsCreatesClientCredentialsProvider(): void
    {
        $categoriesJson = json_encode(ApiMocks::categoriesTreeResponse());
        $tokenJson = json_encode(['access_token' => 'cc-token', 'expires_in' => 3600]);
        $httpClient = $this->createHttpClientWithTokenAndCategories($tokenJson, $categoriesJson);

        $client = new InPostBuyClient($httpClient, 'client-id', 'client-secret', 'org-123', true);

        $categories = $client->getCategories();
        $this->assertCount(1, $categories);
        $this->assertSame('root-uuid', $categories->offsetGet(0)->id);
    }

    public function testAcceptLanguageHeaderIsSentWithSpecifiedLanguage(): void
    {
        $tokenProvider = $this->createStub(AccessTokenProviderInterface::class);
        $tokenProvider->method('getAccessToken')->willReturn('test-token');

        $categoriesJson = json_encode(ApiMocks::categoriesTreeResponse());
        $capturedHeaders = [];

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('request')->willReturnCallback(function (string $method, string $url, array $options = []) use ($categoriesJson, &$capturedHeaders) {
            $capturedHeaders = $options['headers'] ?? [];
            $response = $this->createStub(ResponseInterface::class);
            $response->method('getStatusCode')->willReturn(200);
            $response->method('getContent')->with(false)->willReturn($categoriesJson);
            $response->method('getHeaders')->with(false)->willReturn([]);
            return $response;
        });

        $client = InPostBuyClient::createWithTokenProvider(
            $httpClient,
            $tokenProvider,
            'org-123',
            true,
            Language::English,
        );
        $client->getCategories();

        $this->assertArrayHasKey('Accept-Language', $capturedHeaders);
        $this->assertSame('en', $capturedHeaders['Accept-Language']);
    }

    public function testDefaultLanguageIsPolish(): void
    {
        $tokenProvider = $this->createStub(AccessTokenProviderInterface::class);
        $tokenProvider->method('getAccessToken')->willReturn('test-token');

        $categoriesJson = json_encode(ApiMocks::categoriesTreeResponse());
        $capturedHeaders = [];

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('request')->willReturnCallback(function (string $method, string $url, array $options = []) use ($categoriesJson, &$capturedHeaders) {
            $capturedHeaders = $options['headers'] ?? [];
            $response = $this->createStub(ResponseInterface::class);
            $response->method('getStatusCode')->willReturn(200);
            $response->method('getContent')->with(false)->willReturn($categoriesJson);
            $response->method('getHeaders')->with(false)->willReturn([]);
            return $response;
        });

        $client = InPostBuyClient::createWithTokenProvider($httpClient, $tokenProvider, 'org-123', true);
        $client->getCategories();

        $this->assertArrayHasKey('Accept-Language', $capturedHeaders);
        $this->assertSame('pl', $capturedHeaders['Accept-Language']);
    }

    public function testGetCategoriesWithDepthOutOfRangeThrows(): void
    {
        $tokenProvider = $this->createStub(AccessTokenProviderInterface::class);
        $tokenProvider->method('getAccessToken')->willReturn('test-token');
        $categoriesJson = json_encode(ApiMocks::categoriesTreeResponse());
        $httpClient = $this->createHttpClientReturning(200, $categoriesJson);
        $client = InPostBuyClient::createWithTokenProvider($httpClient, $tokenProvider, 'org-123', true);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Depth should be between 0 and 4, got 10.');

        $client->getCategories(depth: 10);
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
