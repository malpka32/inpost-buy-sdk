<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests\Config;

use malpka32\InPostBuySdk\Config\InPostBuyEndpoints;
use PHPUnit\Framework\TestCase;

final class InPostBuyEndpointsTest extends TestCase
{
    public function testBaseUrlProduction(): void
    {
        $url = InPostBuyEndpoints::baseUrl(false);
        $this->assertSame('https://api.inpost-group.com/inpsa', $url);
    }

    public function testBaseUrlSandbox(): void
    {
        $url = InPostBuyEndpoints::baseUrl(true);
        $this->assertSame('https://stage-api.inpost-group.com/inpsa', $url);
    }

    public function testTokenUrlProduction(): void
    {
        $url = InPostBuyEndpoints::tokenUrl(false);
        $this->assertSame('https://api.inpost-group.com/oauth2/token', $url);
    }

    public function testTokenUrlSandbox(): void
    {
        $url = InPostBuyEndpoints::tokenUrl(true);
        $this->assertSame('https://stage-api.inpost-group.com/oauth2/token', $url);
    }

    public function testAuthorizeUrlProduction(): void
    {
        $url = InPostBuyEndpoints::authorizeUrl(false);
        $this->assertSame('https://account.inpost-group.com/oauth2/authorize', $url);
    }

    public function testAuthorizeUrlSandbox(): void
    {
        $url = InPostBuyEndpoints::authorizeUrl(true);
        $this->assertSame('https://stage-account.inpost-group.com/oauth2/authorize', $url);
    }

    public function testAuthorizeUrlWithOverride(): void
    {
        $custom = 'https://custom.example.com/oauth2/authorize';
        $this->assertSame($custom, InPostBuyEndpoints::authorizeUrl(false, $custom));
        $this->assertSame($custom, InPostBuyEndpoints::authorizeUrl(true, $custom));
    }

    public function testGetAvailableScopes(): void
    {
        $scopes = InPostBuyEndpoints::getAvailableScopes();
        $this->assertContains('api:offers:read', $scopes);
        $this->assertContains('api:orders:write', $scopes);
    }
}
