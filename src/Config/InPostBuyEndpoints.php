<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Config;

/**
 * Zaszyte adresy API InPost Buy (inpsa) dla trybu produkcja i sandbox.
 * Dokumentacja: inpsa-api-portal.inpost-group.com, autoryzacja: dokumentacja InPost (OpenID Connect).
 */
final class InPostBuyEndpoints
{
    /** Produkcja – API InPost Buy */
    public const BASE_URL_PRODUCTION = 'https://api.inpost-group.com/inpsa';
    /** Produkcja – endpoint tokenu JWT (OpenID Connect / Keycloak) */
    public const TOKEN_URL_PRODUCTION = 'https://api.inpost-group.com/oauth2/token';

    /** Sandbox / stage – API InPost Buy */
    public const BASE_URL_SANDBOX = 'https://stage-api.inpost-group.com/inpsa';
    /** Sandbox – endpoint tokenu JWT */
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
