<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Auth;

/**
 * Provides OAuth2 access token for API request authentication.
 */
interface AccessTokenProviderInterface
{
    public function getAccessToken(): string;
}
