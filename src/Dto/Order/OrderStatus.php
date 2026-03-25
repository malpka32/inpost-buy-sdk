<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Order;

/**
 * Order status values (OpenAPI: OrderStatus).
 *
 * Used by `GET .../orders` as `orderStatus` filter and for order status fields.
 */
enum OrderStatus: string
{
    case CREATED = 'CREATED';
    case ACCEPTED = 'ACCEPTED';
    case REFUSED = 'REFUSED';
    case REJECTED = 'REJECTED';
    case CANCELED = 'CANCELED';
    case UNKNOWN = 'UNKNOWN';
}
