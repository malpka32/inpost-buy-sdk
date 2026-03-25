<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Order;

/**
 * Supported statuses for order update command.
 */
enum OrderUpdateStatus: string
{
    case ACCEPTED = 'ACCEPTED';
    case REFUSED = 'REFUSED';
}
