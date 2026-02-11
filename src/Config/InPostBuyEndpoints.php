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

    /** Sandbox / stage – InPost Buy API */
    public const BASE_URL_SANDBOX = 'https://stage-api.inpost-group.com/inpsa';
    /** Sandbox – JWT token endpoint */
    public const TOKEN_URL_SANDBOX = 'https://stage-api.inpost-group.com/oauth2/token';

    public static function baseUrl(bool $sandbox): string
    {
        return $sandbox ? self::BASE_URL_SANDBOX : self::BASE_URL_PRODUCTION;
    }

    public static function tokenUrl(bool $sandbox): string
    {
        return $sandbox ? self::TOKEN_URL_SANDBOX : self::TOKEN_URL_PRODUCTION;
    }
}
