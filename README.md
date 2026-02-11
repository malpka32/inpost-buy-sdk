# InPost Buy SDK (inpsa)

PHP client for the **InPost Buy API** (inpsa) — sell your products through InPost's marketplace. Categories, offers, orders — all wrapped in a clean, type-safe interface.

[![PHP](https://img.shields.io/badge/PHP-%3E%3D8.1-777BB4?logo=php&logoColor=white)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

---

## What is InPost Buy?

InPost Buy (inpsa) lets merchants integrate their product catalog and orders with InPost's platform. You publish offers, receive orders, and update their status — all via REST API. This SDK handles authentication, serialization, and mapping so you focus on business logic.

## Features

- **Categories** — fetch product category tree (read-only)
- **Offers** — create single or batch offers with products, stock, and pricing
- **Orders** — list orders, fetch details, accept or refuse with status updates
- **OAuth2** — automatic token acquisition (client credentials grant) with in-memory caching
- **Typed DTOs** — `OfferDto`, `ProductDto`, `OrderDto` etc., no raw arrays in your code
- **Exceptions** — `NotFoundException`, `BadRequestException`, `ServerException` etc. with HTTP status and error details

---

## Requirements

- PHP 8.1+
- [Symfony HttpClient](https://symfony.com/doc/current/http_client.html) (or any PSR-18-compatible client via Symfony contracts)
- [ramsey/collection](https://github.com/ramsey/collection)

---

## Installation

```bash
composer require malpka32/inpost-buy-sdk
```

---

## Quick Start

```php
<?php

use malpka32\InPostBuySdk\Client\InPostBuyClient;
use Symfony\Component\HttpClient\HttpClient;

$client = new InPostBuyClient(
    httpClient: HttpClient::create(),
    clientId: 'your-client-id',
    clientSecret: 'your-client-secret',
    organizationId: 'your-org-uuid',
    sandbox: true,  // use false for production
);

// Fetch categories
$categories = $client->getCategories();
foreach ($categories as $category) {
    echo $category->name . " (" . $category->id . ")\n";
}

// Fetch offers
$offers = $client->getOffers(offerStatus: ['PUBLISHED'], limit: 20);

// Fetch orders
$orders = $client->getOrders(status: 'CREATED');
```

---

## Usage

### Categories

The category tree is read-only — you use it to assign products to categories when creating offers.

```php
$categories = $client->getCategories();

foreach ($categories as $cat) {
    printf(
        "ID: %s | Name: %s | Leaf: %s\n",
        $cat->id,
        $cat->name,
        $cat->leaf ? 'yes' : 'no'
    );
}
```

### Creating an Offer

An offer is built from nested DTOs: `ProductDto`, `StockDto`, `PriceDto`. Optionally add attributes (e.g. color, size) and dimensions.

```php
use malpka32\InPostBuySdk\Client\InPostBuyClient;
use malpka32\InPostBuySdk\Dto\OfferDto;
use malpka32\InPostBuySdk\Dto\ProductDto;
use malpka32\InPostBuySdk\Dto\StockDto;
use malpka32\InPostBuySdk\Dto\PriceDto;
use malpka32\InPostBuySdk\Dto\DimensionDto;
use malpka32\InPostBuySdk\Collection\AttributeValueCollection;
use malpka32\InPostBuySdk\Dto\AttributeValueDto;

$product = new ProductDto(
    name: 'Cool T-Shirt',
    description: 'Comfortable cotton t-shirt in various sizes.',
    brand: 'MyBrand',
    categoryId: '67909821-cc25-45ec-80ce-5ac4f2f01032',  // from getCategories()
    sku: 'TSHIRT-001',
    ean: '5901234567890',
    attributes: AttributeValueCollection::fromAttributes(
        new AttributeValueDto('attr-color-uuid', ['Red'], 'en'),
        new AttributeValueDto('attr-size-uuid', ['M', 'L'])
    ),
    dimension: new DimensionDto(width: 200, height: 50, length: 300, weight: 200)  // mm, g
);

$offer = new OfferDto(
    externalId: 'SKU-TSHIRT-001',
    product: $product,
    stock: new StockDto(quantity: 10, unit: 'UNIT'),
    price: new PriceDto(amount: 99.99, currency: 'PLN', taxRateInfo: '23%')
);

$offerId = $client->putOffer($offer);
echo "Created offer ID: $offerId\n";
```

### Batch Offers

Create multiple offers in one request:

```php
use malpka32\InPostBuySdk\Collection\OfferCollection;

$offers = OfferCollection::fromOffers($offer1, $offer2, $offer3);
$ids = $client->putOffers($offers);

foreach ($ids as $id) {
    echo "Created: $id\n";
}
```

### Listing and Filtering Offers

```php
$offers = $client->getOffers(
    offerStatus: ['PENDING', 'PUBLISHED'],
    limit: 50,
    offset: 0,
    sort: ['-updatedAt']
);

foreach ($offers as $offer) {
    echo $offer->externalId . " – " . $offer->product->name . "\n";
}
```

### Orders

```php
// List orders (optionally filter by status)
$orders = $client->getOrders(status: 'CREATED');

foreach ($orders as $order) {
    echo $order->inpostOrderId . " – " . ($order->reference ?? 'no ref') . "\n";
}

// Fetch single order
$order = $client->getOrder('order-uuid-from-inpost');
if ($order !== null) {
    var_dump($order->status, $order->items);
}

// Accept order
use malpka32\InPostBuySdk\Dto\OrderStatusDto;

$client->updateOrderStatus('order-uuid', new OrderStatusDto(status: 'ACCEPTED'));

// Refuse with reason
$client->updateOrderStatus('order-uuid', new OrderStatusDto(
    status: 'REFUSED',
    comment: 'Out of stock'
));
```

---

## Error Handling

The SDK throws specific exceptions for HTTP errors:

| Exception | HTTP |
|-----------|------|
| `BadRequestException` | 400 |
| `UnauthorizedException` | 401 |
| `ForbiddenException` | 403 |
| `NotFoundException` | 404 |
| `UnprocessableEntityException` | 422 |
| `TooManyRequestsException` | 429 |
| `ServerException` | 5xx |

All extend `malpka32\InPostBuySdk\Exception\ApiException` and provide:

- `getStatusCode()` — HTTP status code
- `getResponseBody()` — raw response body
- `getErrorResponse()` — parsed error (errorCode, errorMessage, details)
- `isRetryable()` — true for 5xx and 429
- `getRetryAfterSeconds()` — from `Retry-After` header when available

```php
use malpka32\InPostBuySdk\Exception\NotFoundException;
use malpka32\InPostBuySdk\Exception\ApiException;

try {
    $order = $client->getOrder('non-existent');
} catch (NotFoundException $e) {
    echo "Order not found: " . $e->getMessage();
} catch (ApiException $e) {
    echo "API error: " . $e->getStatusCode();
    if ($e->isRetryable()) {
        echo " – retry after " . ($e->getRetryAfterSeconds() ?? '?') . " seconds";
    }
}
```

---

## Testing

```bash
composer test
```

Or with Docker:

```bash
docker compose run --rm test
```

---

## API Documentation

Official InPost Buy (inpsa) API docs: [inpsa-api-portal.inpost-group.com](https://inpsa-api-portal.inpost-group.com/)

---

## Support the project

If this library helps you, consider buying me a coffee — it allows me to maintain and update the library alongside InPost API changes.

→ [buycoffee.to/malpka32](https://buycoffee.to/malpka32)

<a href="https://buycoffee.to/malpka32">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="2 2 33 33" width="150" height="150" shape-rendering="crispEdges"><rect x="2" y="2" width="33" height="33" rx="3" ry="3" fill="#fff"/><path d="M4 4h7v1h-7zM13 4h1v1h-1zM15 4h2v1h-2zM19 4h2v1h-2zM22 4h3v1h-3zM26 4h7v1h-7zM4 5h1v1h-1zM10 5h1v1h-1zM13 5h2v1h-2zM22 5h1v1h-1zM24 5h1v1h-1zM26 5h1v1h-1zM32 5h1v1h-1zM4 6h1v1h-1zM6 6h3v1h-3zM10 6h1v1h-1zM12 6h2v1h-2zM15 6h1v1h-1zM18 6h1v1h-1zM20 6h1v1h-1zM24 6h1v1h-1zM26 6h1v1h-1zM28 6h3v1h-3zM32 6h1v1h-1zM4 7h1v1h-1zM6 7h3v1h-3zM10 7h1v1h-1zM12 7h3v1h-3zM20 7h2v1h-2zM23 7h1v1h-1zM26 7h1v1h-1zM28 7h3v1h-3zM32 7h1v1h-1zM4 8h1v1h-1zM6 8h3v1h-3zM10 8h1v1h-1zM12 8h3v1h-3zM17 8h3v1h-3zM21 8h4v1h-4zM26 8h1v1h-1zM28 8h3v1h-3zM32 8h1v1h-1zM4 9h1v1h-1zM10 9h1v1h-1zM12 9h9v1h-9zM24 9h1v1h-1zM26 9h1v1h-1zM32 9h1v1h-1zM4 10h7v1h-7zM12 10h1v1h-1zM14 10h1v1h-1zM16 10h1v1h-1zM18 10h1v1h-1zM20 10h1v1h-1zM22 10h1v1h-1zM24 10h1v1h-1zM26 10h7v1h-7zM12 11h1v1h-1zM14 11h1v1h-1zM17 11h1v1h-1zM19 11h1v1h-1zM22 11h3v1h-3zM4 12h1v1h-1zM6 12h5v1h-5zM14 12h3v1h-3zM18 12h2v1h-2zM21 12h1v1h-1zM23 12h1v1h-1zM26 12h5v1h-5zM6 13h2v1h-2zM12 13h1v1h-1zM14 13h2v1h-2zM19 13h2v1h-2zM23 13h1v1h-1zM25 13h4v1h-4zM32 13h1v1h-1zM8 14h1v1h-1zM10 14h1v1h-1zM12 14h1v1h-1zM16 14h1v1h-1zM22 14h1v1h-1zM24 14h1v1h-1zM26 14h2v1h-2zM5 15h1v1h-1zM12 15h2v1h-2zM16 15h1v1h-1zM18 15h1v1h-1zM20 15h1v1h-1zM22 15h3v1h-3zM26 15h1v1h-1zM28 15h2v1h-2zM31 15h1v1h-1zM4 16h1v1h-1zM9 16h6v1h-6zM20 16h2v1h-2zM23 16h1v1h-1zM29 16h2v1h-2zM4 17h1v1h-1zM6 17h1v1h-1zM9 17h1v1h-1zM11 17h2v1h-2zM15 17h5v1h-5zM21 17h8v1h-8zM32 17h1v1h-1zM4 18h3v1h-3zM10 18h1v1h-1zM12 18h11v1h-11zM27 18h4v1h-4zM4 19h2v1h-2zM9 19h1v1h-1zM13 19h1v1h-1zM16 19h2v1h-2zM19 19h1v1h-1zM24 19h3v1h-3zM31 19h1v1h-1zM4 20h1v1h-1zM7 20h2v1h-2zM10 20h1v1h-1zM13 20h2v1h-2zM16 20h1v1h-1zM18 20h2v1h-2zM21 20h1v1h-1zM24 20h1v1h-1zM27 20h1v1h-1zM29 20h2v1h-2zM4 21h3v1h-3zM11 21h1v1h-1zM13 21h1v1h-1zM15 21h1v1h-1zM19 21h2v1h-2zM22 21h7v1h-7zM30 21h1v1h-1zM32 21h1v1h-1zM4 22h1v1h-1zM7 22h1v1h-1zM9 22h2v1h-2zM15 22h1v1h-1zM22 22h1v1h-1zM27 22h2v1h-2zM30 22h1v1h-1zM4 23h1v1h-1zM8 23h2v1h-2zM11 23h2v1h-2zM14 23h1v1h-1zM16 23h1v1h-1zM18 23h1v1h-1zM20 23h1v1h-1zM23 23h6v1h-6zM31 23h1v1h-1zM4 24h1v1h-1zM6 24h1v1h-1zM10 24h2v1h-2zM14 24h1v1h-1zM16 24h1v1h-1zM20 24h9v1h-9zM30 24h3v1h-3zM12 25h1v1h-1zM15 25h5v1h-5zM24 25h1v1h-1zM28 25h5v1h-5zM4 26h7v1h-7zM14 26h2v1h-2zM17 26h8v1h-8zM26 26h1v1h-1zM28 26h3v1h-3zM4 27h1v1h-1zM10 27h1v1h-1zM12 27h1v1h-1zM16 27h2v1h-2zM19 27h1v1h-1zM24 27h1v1h-1zM28 27h1v1h-1zM4 28h1v1h-1zM6 28h3v1h-3zM10 28h1v1h-1zM12 28h5v1h-5zM18 28h2v1h-2zM21 28h2v1h-2zM24 28h5v1h-5zM30 28h1v1h-1zM32 28h1v1h-1zM4 29h1v1h-1zM6 29h3v1h-3zM10 29h1v1h-1zM12 29h2v1h-2zM15 29h1v1h-1zM19 29h2v1h-2zM23 29h1v1h-1zM27 29h1v1h-1zM29 29h2v1h-2zM4 30h1v1h-1zM6 30h3v1h-3zM10 30h1v1h-1zM12 30h4v1h-4zM17 30h1v1h-1zM21 30h3v1h-3zM25 30h7v1h-7zM4 31h1v1h-1zM10 31h1v1h-1zM17 31h2v1h-2zM20 31h1v1h-1zM22 31h1v1h-1zM24 31h1v1h-1zM29 31h1v1h-1zM31 31h1v1h-1zM4 32h7v1h-7zM12 32h1v1h-1zM14 32h2v1h-2zM18 32h1v1h-1zM22 32h2v1h-2zM28 32h1v1h-1zM30 32h1v1h-1z" fill="#000"/></svg>
</a>

---

## License

MIT
