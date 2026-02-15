<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Auth;

/**
 * Storage for OAuth2 tokens (access_token, refresh_token, expires_at).
 * Consumer (e.g. PrestaShop module) provides implementation backed by its persistence.
 */
interface TokenStorageInterface
{
    /**
     * Persists tokens after exchange or refresh.
     *
     * @param string $accessToken  Non-empty access token
     * @param string $refreshToken Non-empty refresh token
     * @param int    $expiresAt   Unix timestamp when access_token expires
     */
    public function setTokens(string $accessToken, string $refreshToken, int $expiresAt): void;

    /**
     * Returns stored access_token or null if not found.
     */
    public function getAccessToken(): ?string;

    /**
     * Returns stored refresh_token or null if not found.
     */
    public function getRefreshToken(): ?string;

    /**
     * Returns Unix timestamp when access_token expires, or null if not stored.
     */
    public function getExpiresAt(): ?int;
}
