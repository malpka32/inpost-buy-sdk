<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Auth;

use malpka32\InPostBuySdk\Exception\ApiException;

/**
 * Token provider for PKCE flow. Reads tokens from storage, refreshes when expired.
 */
final class PkceTokenProvider implements AccessTokenProviderInterface
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly PkceOAuth2Client $pkceOAuth2Client,
        private readonly string $clientId,
        private readonly string $clientSecret,
        private readonly string $tokenUrl,
        private readonly int $expiryBufferSeconds = 60,
    ) {
    }

    public function getAccessToken(): string
    {
        $accessToken = $this->tokenStorage->getAccessToken();
        $expiresAt = $this->tokenStorage->getExpiresAt();

        if ($accessToken !== null && $expiresAt !== null && time() < $expiresAt - $this->expiryBufferSeconds) {
            return $accessToken;
        }

        $refreshToken = $this->tokenStorage->getRefreshToken();
        if ($refreshToken === null || $refreshToken === '') {
            throw new ApiException('No refresh token available – re-authorization required');
        }

        return $this->pkceOAuth2Client->refreshAccessToken(
            $refreshToken,
            $this->clientId,
            $this->clientSecret,
            $this->tokenUrl,
            $this->tokenStorage,
        );
    }
}
