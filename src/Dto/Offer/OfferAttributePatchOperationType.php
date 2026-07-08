<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer;

/**
 * Type of a single Offer attributes patch operation.
 *
 * - UPSERT – add or overwrite an attribute value (by attribute id)
 * - REMOVE – delete an attribute (by attribute id)
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html#tag/Offers/operation/patchOffersAttributesV1
 */
enum OfferAttributePatchOperationType: string
{
    case UPSERT = 'UPSERT';
    case REMOVE = 'REMOVE';
}
