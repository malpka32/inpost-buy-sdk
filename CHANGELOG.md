# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.1.0] - 2025-02-11

### Added

- Full InPost Buy (inpsa) API client with categories, offers, and orders
- OAuth2 authentication (client credentials grant) with token caching
- Typed DTOs: `OfferDto`, `ProductDto`, `StockDto`, `PriceDto`, `OrderDto`, `OrderStatusDto`, `CategoryDto`, `DimensionDto`, `AttributeValueDto`
- Collections: `CategoryCollection`, `OfferCollection`, `OfferIdCollection`, `OrderCollection`, `AttributeValueCollection`
- Response mappers for API → DTO mapping with `OfferResponseMapper`, `CategoryResponseMapper`, `OrderResponseMapper`
- HTTP exceptions: `BadRequestException`, `UnauthorizedException`, `ForbiddenException`, `NotFoundException`, `UnprocessableEntityException`, `TooManyRequestsException`, `ServerException` with `getStatusCode()`, `getErrorResponse()`, `isRetryable()`, `getRetryAfterSeconds()`
- Endpoint interfaces and fake endpoints for testing
- PHPUnit test suite (62 tests) with Docker support
- README with installation, Quick Start, and usage examples
- Support section with buycoffee.to link

### Changed

- `OfferDto` rebuilt with nested DTOs (`product`, `stock`, `price`) instead of flat structure
- Removed `OfferProposalBuilder` and `OfferRequestMapper` — use `OfferDto::toArray()` directly
- Removed `$since` parameter from `getOrders()`
- All code comments translated from Polish to English

### Removed

- `OfferProposalBuilder`
- `OfferRequestMapper`

[0.1.0]: https://github.com/malpka32/inpost-buy-sdk/releases/tag/v0.1.0
