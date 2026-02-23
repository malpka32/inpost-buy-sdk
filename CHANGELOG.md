# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.6.0] - 2026-02-23

### Added

- **Offer Attachments** — list, create, download, delete attachments (images etc.)
- **Category tree** — `getCategories()` returns `CategoryTreeCollection` with hierarchy (roots + `children`), single endpoint
- **Category details** — `getCategory()`, `getCategoryAttributes()` — required/optional attributes when creating offers
- **Extended offers** — `getOfferDetails()`, `closeOffer()`, `reopenOffer()`, `getOfferCommandStatus()`, `getOfferEvents()`, `getOfferHint()`, `getDepositTypes()`
- **Accept-Language** — `Language` enum (pl/en) for API response language
- New mapper architecture — grouping in `Mapper/Offer`, `Mapper/Category`, `Mapper/Order`, `Mapper/Attribute`, `Mapper/Attachment`
- DTO namespaces: `Dto/Offer`, `Dto/Category`, `Dto/Order`, `Dto/Attribute`, `Dto/Common`
- `OfferDetailsDto`, `OfferPutResultDto`, `OfferEventsResultDto`, `OfferHintResultDto`, `CommandStatusDto`
- `CategoryTreeBuilder`, `CategoryTreeCollection`, `CategoryTreeNode`
- `AttributeDefinitionCollection`, `AttributeDefinitionDto`

### Changed

- `getCategories()` returns `CategoryTreeCollection` (formerly flat list) — hierarchy from API
- DTO and mapper refactoring — new directory structure
- PHPStan level 10

---

## [0.2.0] - 2025-02-15

### Added

- **OAuth2 PKCE (Authorization Code flow)** — support for OAuth2-based integrations with PKCE (e.g. PrestaShop modules for merchants)
- `PkceOAuth2Client` — authorization initiation (`initiateAuthorization`), code-for-tokens exchange (`exchangeCodeForTokens`), refresh (`refreshAccessToken`)
- `PkceTokenProvider` — token provider for PKCE flow with automatic refresh before expiry
- `PkceStateStorageInterface`, `TokenStorageInterface` — storage abstractions (consumer provides implementation, e.g. PrestaShop Configuration)
- `InPostBuyEndpoints::authorizeUrl()` — authorize endpoint URL (sandbox/production) with optional override
- Constants `AUTHORIZE_URL_SANDBOX`, `AUTHORIZE_URL_PRODUCTION`, `SCOPES_DEFAULT`, `getAvailableScopes()` — OAuth2 configuration
- `InPostBuyClient::createWithTokenProvider()` — client factory with any `AccessTokenProviderInterface` (client credentials remains default)
- Composer `ci` script — full check: PHPStan, cs-check, tests

### Changed

- `InPostBuyClient` — constructor optionally accepts `AccessTokenProviderInterface` (6th parameter) for backward compatibility

---

## [0.1.0] - 2025-02-11

### Added

- GitHub Actions CI — PHPStan, php-cs-fixer (PSR-12), tests with coverage for PHP 8.1–8.3
- PHP-CS-Fixer config (PSR-12, `declare(strict_types)`, array syntax)
- PHPStan config (max level)
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


[0.6.0]: https://github.com/malpka32/inpost-buy-sdk/releases/tag/v0.6.0
[0.2.0]: https://github.com/malpka32/inpost-buy-sdk/releases/tag/v0.2.0
[0.1.0]: https://github.com/malpka32/inpost-buy-sdk/releases/tag/v0.1.0
