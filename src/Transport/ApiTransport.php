<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Transport;

use malpka32\InPostBuySdk\Auth\AccessTokenProviderInterface;
use malpka32\InPostBuySdk\Exception\ApiExceptionFactory;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * HTTP transport layer with Bearer authentication.
 * Responsibility: sending requests with token, API error handling.
 */
final class ApiTransport
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly AccessTokenProviderInterface $tokenProvider,
    ) {
    }

    /**
     * @param array<string, mixed>|list<array<string, mixed>>|null $body
     */
    public function request(string $method, string $url, ?array $body = null): ResponseInterface
    {
        $token = $this->tokenProvider->getAccessToken();
        $contentType = $method === 'PATCH' ? 'application/merge-patch+json' : 'application/json';
        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => $contentType,
            ],
        ];
        if ($body !== null && in_array($method, ['POST', 'PUT', 'PATCH'], true)) {
            $options['json'] = $body;
        }

        $response = $this->httpClient->request($method, $url, $options);
        if ($response->getStatusCode() >= 400) {
            throw ApiExceptionFactory::fromResponse(
                $response,
                sprintf('API error: %s %s', $method, $url)
            );
        }

        return $response;
    }
}
