<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Order;

/**
 * Supported payment statuses for orders listing.
 */
enum OrderPaymentStatus: string
{
    case PAID = 'PAID';
    case NOT_PAID = 'NOT_PAID';
}
