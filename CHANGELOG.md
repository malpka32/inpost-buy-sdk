# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.7.5] - 2026-03-25

### Added

- Added `OrderEventCollection::reverse()` to return a new collection with reversed event order.
- Added unit test coverage for reversing order event collections.

### Changed

- Replaced Polish comments/PHPDoc in DTOs and exceptions with English wording for codebase consistency.

---

## [0.7.4] - 2026-03-25

### Changed

- Fixed strict scalar normalization in order event DTO mapping:
  - replaced direct `(string)` casts from mixed values with `ArrayHelper::asString()`
  - tightened nested `order` payload typing in `OrderEventDto`/`OrderEventOrderDto` for PHPStan compatibility

---

## [0.7.3] - 2026-03-25

### Added

- `OrderPaymentType` enum completed with documented values:
  `CARD`, `CARD_TOKEN`, `GOOGLE_PAY`, `APPLE_PAY`, `BLIK_CODE`, `BLIK_TOKEN`,
  `PAY_BY_LINK`, `SHOPPING_LIMIT`, `DEFERRED_PAYMENT`, `CASH_ON_DELIVERY`, `UNKNOWN`.
- New `OrderEventCollection` for strongly typed order events results.
- New `OrderEventOrderDto` for nested `order` object in event payloads.
- New shared `DateTimeHelper::parseOrNull()` to centralize nullable datetime parsing.

### Changed

- `OrderEventsResultDto` now returns `OrderEventCollection` instead of raw array.
- `OrderEventDto` now maps typed fields (`id`, `order`, `eventType`, `occurredAt`) while preserving raw payload.
- `OrderPaymentDto` and `OrderPaymentDetailsDto` now support `OrderPaymentType|string|null`.
- Payment type normalization moved from mappers into `OrderPaymentType::fromRaw()`.
- Replaced duplicated datetime parsing in order mappers/DTO mapping with `DateTimeHelper`.
- Updated fixtures and tests for typed order events and payment type mapping.

---

## [0.7.2] - 2026-03-25

### Changed

- Aligned changelog entries to reflect the real scope of tagged releases (`0.7.0`, `0.7.1`).
- Bumped package version metadata to `0.7.2`.

---

## [0.7.1] - 2026-03-25

### Changed

- Fixed strict PHPStan issues in `Order` mappers (`missingType.generics`, array shape typing).
- Tightened mapper constructor typing and array annotations without changing runtime behavior.
- Applied formatting fixes required by `cs-check` for CI consistency.

---

## [0.7.0] - 2026-03-25

### Added

- Extended Orders API support:
  - order list filters: `paymentStatus`, `limit`, `offset`, `sort`
  - order command status endpoint
  - order events endpoint
- New typed Order DTO model:
  - `OrderLineDto` and `OrderLineCollection`
  - `OrderDeliveryParcelCollection` and `OrderPaymentCollection`
  - new `Order/Core` DTOs and dedicated mappers
- New enums for typed API usage:
  - `OrderStatus`, `OrderPaymentStatus`, `OrderEventType`, `OrderUpdateStatus`
  - `OfferStatus`, `OfferEventType`, `AttachmentType`
  - shared `ListSort` for offers and orders list endpoints

### Changed

- Refactored Order mappers into `Order/Core` and `Order/Line` architecture.
- `OrderDto` status is now strongly typed to `OrderStatus`.
- Offer and Order filters/events are now enum-friendly across client, repository, and endpoint contracts.
- README examples updated to enum-based usage.
- Mapping normalized to documented OpenAPI camelCase fields.
- Removed `.php-cs-fixer.cache` from repository tracking and ignored `composer.lock` for library workflow.

---

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


[0.7.4]: https://github.com/malpka32/inpost-buy-sdk/releases/tag/v0.7.4
[0.7.3]: https://github.com/malpka32/inpost-buy-sdk/releases/tag/v0.7.3
[0.7.2]: https://github.com/malpka32/inpost-buy-sdk/releases/tag/v0.7.2
[0.7.1]: https://github.com/malpka32/inpost-buy-sdk/releases/tag/v0.7.1
[0.7.0]: https://github.com/malpka32/inpost-buy-sdk/releases/tag/v0.7.0
[0.6.0]: https://github.com/malpka32/inpost-buy-sdk/releases/tag/v0.6.0
[0.2.0]: https://github.com/malpka32/inpost-buy-sdk/releases/tag/v0.2.0
[0.1.0]: https://github.com/malpka32/inpost-buy-sdk/releases/tag/v0.1.0
