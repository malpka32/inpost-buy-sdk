<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Order;

/**
 * Supported order event types from InPost API.
 */
enum OrderEventType: string
{
    case CREATED = 'CREATED';
    case ACCEPTED = 'ACCEPTED';
    case REFUSED = 'REFUSED';
    case REJECTED = 'REJECTED';
    case CANCELLED = 'CANCELLED';
    case PAID = 'PAID';
    case PARTIALLY_REFUNDED = 'PARTIALLY_REFUNDED';
    case REFUNDED = 'REFUNDED';
    case SHIPPED = 'SHIPPED';
    case RETURN_CREATED = 'RETURN_CREATED';
    case UPDATED = 'UPDATED';
}
