<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Common;

/**
 * Shared sort values for list endpoints.
 */
enum ListSort: string
{
    case STATUS_ASC = 'status';
    case STATUS_DESC = '-status';
    case UPDATED_AT_ASC = 'updatedAt';
    case UPDATED_AT_DESC = '-updatedAt';
    case CREATED_AT_ASC = 'createdAt';
    case CREATED_AT_DESC = '-createdAt';
}
