<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Collection;

use malpka32\InPostBuySdk\Dto\Offer\Attachment\AttachmentDto;
use Ramsey\Collection\AbstractCollection;

/**
 * @extends AbstractCollection<AttachmentDto>
 */
final class AttachmentCollection extends AbstractCollection
{
    public function getType(): string
    {
        return \malpka32\InPostBuySdk\Dto\Offer\Attachment\AttachmentDto::class;
    }
}
