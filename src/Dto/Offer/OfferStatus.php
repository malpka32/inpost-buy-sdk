<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer;

/**
 * Known offer statuses from InPost API.
 */
enum OfferStatus: string
{
    case PENDING = 'PENDING';
    case REJECTED = 'REJECTED';
    case PUBLISHED = 'PUBLISHED';
    case CLOSED = 'CLOSED';
    case SOLDOUT = 'SOLDOUT';
}
