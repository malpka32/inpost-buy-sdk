# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.2.0] - 2025-02-15

### Added

- **OAuth2 PKCE (Authorization Code flow)** — wsparcie dla integracji opartych o OAuth2 z PKCE (np. moduły PrestaShop dla merchantów)
- `PkceOAuth2Client` — inicjacja autoryzacji (`initiateAuthorization`), wymiana kodu na tokeny (`exchangeCodeForTokens`), odświeżanie (`refreshAccessToken`)
- `PkceTokenProvider` — provider tokenów dla flow PKCE z automatycznym odświeżaniem przed wygaśnięciem
- `PkceStateStorageInterface`, `TokenStorageInterface` — abstrakcje storage (konsument dostarcza implementację, np. PrestaShop Configuration)
- `InPostBuyEndpoints::authorizeUrl()` — URL endpointu authorize (sandbox/production) z opcjonalnym override
- Stałe `AUTHORIZE_URL_SANDBOX`, `AUTHORIZE_URL_PRODUCTION`, `SCOPES_DEFAULT`, `getAvailableScopes()` — konfiguracja OAuth2
- `InPostBuyClient::createWithTokenProvider()` — fabryka klienta z dowolnym `AccessTokenProviderInterface` (client credentials pozostaje domyślny)
- Skrypt `ci` w composer — pełna kontrola: PHPStan, cs-check, testy

### Changed

- `InPostBuyClient` — konstruktor przyjmuje opcjonalnie `AccessTokenProviderInterface` (6. parametr) dla backward compatibility

---

## [0.1.0] - 2025-02-11

### Added

- GitHub Actions CI — PHPStan, php-cs-fixer (PSR-12), testy z pokryciem dla PHP 8.1–8.3
- Konfiguracja PHP-CS-Fixer (PSR-12, `declare(strict_types)`, składnia tablic)
- Konfiguracja PHPStan (poziom max)
- Full InPost Buy (inpsa) API client with categories, offers, and orders
- OAuth2 authentication (client credentials grant) with token caching
- Typed DTOs: `OfferDto`, `ProductDto`, `StockDto`, `PriceDto`, `OrderDto`, `OrderStatusDto`, `CategoryDto`, `DimensionDto`, `AttributeValueDto`, `ErrorResponseDto`, `ErrorDetailDto`
- Collections: `CategoryCollection`, `OfferCollection`, `OfferIdCollection`, `OrderCollection`, `AttributeValueCollection`
- Response mappers for API → DTO mapping with `OfferResponseMapper`, `CategoryResponseMapper`, `OrderResponseMapper`
- HTTP exceptions: `BadRequestException`, `UnauthorizedException`, `ForbiddenException`, `NotFoundException`, `UnprocessableEntityException`, `TooManyRequestsException`, `UnsupportedMediaTypeException`, `ServerException` with `getStatusCode()`, `getErrorResponse()`, `isRetryable()`, `getRetryAfterSeconds()`
- Endpoint interfaces and fake endpoints for testing (`FakeCategoriesEndpoint`, `FakeOffersEndpoint`, `FakeOrdersEndpoint`)
- Test fixtures: `ApiFaker`, `ApiMocks` for generating mock API data
- PHPUnit test suite (62 tests) with Docker support (Dockerfile, docker-compose)
- Composer scripts: `test`, `test:coverage`, `phpstan`, `cs-fix`, `cs-check`
- README with installation, Quick Start, and usage examples
- Support section with buycoffee.to link


[0.2.0]: https://github.com/malpka32/inpost-buy-sdk/releases/tag/v0.2.0
[0.1.0]: https://github.com/malpka32/inpost-buy-sdk/releases/tag/v0.1.0
