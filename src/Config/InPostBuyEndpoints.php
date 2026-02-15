<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Config;

/**
 * Hardcoded InPost Buy (inpsa) API URLs for production and sandbox.
 * Documentation: inpsa-api-portal.inpost-group.com, auth: InPost docs (OpenID Connect).
 */
final class InPostBuyEndpoints
{
    /** Production – InPost Buy API */
    public const BASE_URL_PRODUCTION = 'https://api.inpost-group.com/inpsa';
    /** Production – JWT token endpoint (OpenID Connect / Keycloak) */
    public const TOKEN_URL_PRODUCTION = 'https://api.inpost-group.com/oauth2/token';
    /** Production – OAuth2 authorize endpoint */
    public const AUTHORIZE_URL_PRODUCTION = 'https://account.inpost-group.com/oauth2/authorize';

    /** Sandbox / stage – InPost Buy API */
    public const BASE_URL_SANDBOX = 'https://stage-api.inpost-group.com/inpsa';
    /** Sandbox – JWT token endpoint */
    public const TOKEN_URL_SANDBOX = 'https://stage-api.inpost-group.com/oauth2/token';
    /** Sandbox – OAuth2 authorize endpoint */
    public const AUTHORIZE_URL_SANDBOX = 'https://stage-account.inpost-group.com/oauth2/authorize';

    /** Default scopes for InPost Buy API (Authorization Code + PKCE flow) */
    public const SCOPES_DEFAULT = [
        'api:categories:read',
        'api:offers:read',
        'api:offers:write',
        'api:orders:read',
        'api:orders:write',
        'api:points:read',
        'api:shipments:write',
        'api:shipments:read',
        'api:tracking:read',
    ];

    public static function baseUrl(bool $sandbox): string
    {
        return $sandbox ? self::BASE_URL_SANDBOX : self::BASE_URL_PRODUCTION;
    }

    public static function tokenUrl(bool $sandbox): string
    {
        return $sandbox ? self::TOKEN_URL_SANDBOX : self::TOKEN_URL_PRODUCTION;
    }

    /**
     * Returns OAuth2 authorize URL for initiating the Authorization Code flow.
     *
     * @param bool        $sandbox        Use sandbox/stage environment
     * @param string|null $override       Optional custom URL (overrides default for given sandbox)
     */
    public static function authorizeUrl(bool $sandbox, ?string $override = null): string
    {
        return $override ?? ($sandbox ? self::AUTHORIZE_URL_SANDBOX : self::AUTHORIZE_URL_PRODUCTION);
    }

    /**
     * Returns list of available InPost Buy API scopes.
     *
     * @return list<string>
     */
    public static function getAvailableScopes(): array
    {
        return self::SCOPES_DEFAULT;
    }
}
