<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Client;

use malpka32\InPostBuySdk\Config\InPostBuyEndpoints;
use malpka32\InPostBuySdk\Dto\CategoryDto;
use malpka32\InPostBuySdk\Dto\OfferDto;
use malpka32\InPostBuySdk\Dto\OrderDto;
use malpka32\InPostBuySdk\Dto\OrderStatusDto;
use malpka32\InPostBuySdk\Exception\ApiException;
use malpka32\InPostBuySdk\Mapper\CategoryResponseMapper;
use malpka32\InPostBuySdk\Mapper\OrderResponseMapper;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Klient REST API InPost Buy (OpenAPI 1.3.1).
 * Odpowiedzialność: HTTP, token OAuth2, delegacja mapowania do Builder/Mapper.
 */
final class InPostBuyClient implements InPostBuyClientInterface
{
    private ?string $accessToken = null;
    private string $baseUrl;
    private string $tokenUrl;
    private string $organizationPath;

    private readonly OfferProposalBuilder $offerProposalBuilder;
    private readonly CategoryResponseMapper $categoryMapper;
    private readonly OrderResponseMapper $orderMapper;

    public function __construct(
        private HttpClientInterface $httpClient,
        private string $clientId,
        private string $clientSecret,
        private string $organizationId,
        bool $sandbox = false,
    ) {
        $this->baseUrl = InPostBuyEndpoints::baseUrl($sandbox);
        $this->tokenUrl = InPostBuyEndpoints::tokenUrl($sandbox);
        $this->organizationPath = '/v1/organizations/' . rawurlencode($organizationId);
        $this->offerProposalBuilder = new OfferProposalBuilder();
        $this->categoryMapper = new CategoryResponseMapper();
        $this->orderMapper = new OrderResponseMapper();
    }

    public function getCategories(): array
    {
        $response = $this->request('GET', $this->baseUrl . '/v1/categories');
        return $this->categoryMapper->map($this->decodeJson($response));
    }

    public function putCategory(CategoryDto $dto): string
    {
        throw new ApiException('InPost Buy API nie obsługuje tworzenia/edycji kategorii – tylko odczyt drzewa.');
    }

    public function putOffer(OfferDto $dto): string
    {
        $payload = $this->offerProposalBuilder->build($dto);
        $offersPath = $this->organizationPath . '/offers';

        if ($dto->inpostOfferId !== null && $dto->inpostOfferId !== '') {
            $response = $this->request(
                'PATCH',
                $this->baseUrl . $offersPath . '/' . rawurlencode($dto->inpostOfferId),
                $payload
            );
        } else {
            $response = $this->request('POST', $this->baseUrl . $offersPath, $payload);
        }

        $data = $this->decodeJson($response);
        return $data['id'] ?? $data['offerId'] ?? (string) $response->getStatusCode();
    }

    public function getOrders(?\DateTimeInterface $since = null, ?string $status = null): array
    {
        $url = $this->baseUrl . $this->organizationPath . '/orders';
        if ($status !== null) {
            $url .= '?' . http_build_query(['orderStatus' => $status]);
        }
        $response = $this->request('GET', $url);
        return $this->orderMapper->mapList($this->decodeJson($response));
    }

    public function getOrder(string $inpostOrderId): ?OrderDto
    {
        try {
            $url = $this->baseUrl . $this->organizationPath . '/orders/' . rawurlencode($inpostOrderId);
            $response = $this->request('GET', $url);
            return $this->orderMapper->mapItem($this->decodeJson($response));
        } catch (ApiException $e) {
            if ($e->getStatusCode() === 404) {
                return null;
            }
            throw $e;
        }
    }

    public function updateOrderStatus(string $inpostOrderId, OrderStatusDto $status): void
    {
        $url = $this->baseUrl . $this->organizationPath . '/orders/' . rawurlencode($inpostOrderId);
        $lower = strtolower($status->status);
        if ($lower === 'accept' || $lower === 'accepted') {
            $this->request('POST', $url . '/accept', []);
        } elseif ($lower === 'refuse' || $lower === 'refused') {
            $this->request('POST', $url . '/refuse', ['reason' => $status->comment ?? '']);
        }
    }

    private function request(string $method, string $url, ?array $body = null): ResponseInterface
    {
        $token = $this->getAccessToken();
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
            throw new ApiException(
                sprintf('API error: %s %s', $method, $url),
                $response->getStatusCode(),
                $response->getContent(false),
            );
        }
        return $response;
    }

    private function getAccessToken(): string
    {
        if ($this->accessToken !== null) {
            return $this->accessToken;
        }

        $response = $this->httpClient->request('POST', $this->tokenUrl, [
            'body' => http_build_query([
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]),
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
        ]);

        if ($response->getStatusCode() >= 400) {
            throw new ApiException(
                'OAuth2 token request failed',
                $response->getStatusCode(),
                $response->getContent(false),
            );
        }

        $data = $this->decodeJson($response);
        $this->accessToken = $data['access_token'] ?? '';
        if ($this->accessToken === '') {
            throw new ApiException('Missing access_token in OAuth2 response');
        }
        return $this->accessToken;
    }

    private function decodeJson(ResponseInterface $response): array
    {
        $content = $response->getContent(false);
        if ($content === '') {
            return [];
        }
        $decoded = json_decode($content, true);
        return is_array($decoded) ? $decoded : [];
    }
}
