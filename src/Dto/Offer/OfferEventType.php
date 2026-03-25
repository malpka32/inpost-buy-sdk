<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer;

/**
 * Supported offer event types from InPost API.
 */
enum OfferEventType: string
{
    case CREATED = 'CREATED';
    case REJECTED = 'REJECTED';
    case REOPENED = 'REOPENED';
    case CLOSED = 'CLOSED';
    case PUBLISHED = 'PUBLISHED';
    case VALIDATION_FAILED = 'VALIDATION_FAILED';
    case SOLDOUT = 'SOLDOUT';
    case UPDATED = 'UPDATED';
}
