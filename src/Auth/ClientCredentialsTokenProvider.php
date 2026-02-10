<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Auth;

use malpka32\InPostBuySdk\Exception\ApiException;
use malpka32\InPostBuySdk\Exception\ApiExceptionFactory;
use malpka32\InPostBuySdk\Mapper\ArrayHelper;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * OAuth2 token provider (grant: client_credentials) with caching.
 */
final class ClientCredentialsTokenProvider implements AccessTokenProviderInterface
{
    private ?string $accessToken = null;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $tokenUrl,
        private readonly string $clientId,
        private readonly string $clientSecret,
    ) {
    }

    public function getAccessToken(): string
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
            throw ApiExceptionFactory::fromResponse($response, 'OAuth2 token request failed');
        }

        $data = json_decode($response->getContent(false), true);
        $raw = is_array($data) ? ($data['access_token'] ?? '') : '';
        $this->accessToken = ArrayHelper::asString($raw);

        if ($this->accessToken === '') {
            throw new ApiException('Missing access_token in OAuth2 response');
        }

        return $this->accessToken;
    }
}
