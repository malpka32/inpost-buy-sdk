<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Attachment;

use malpka32\InPostBuySdk\Collection\AttachmentCollection;
use malpka32\InPostBuySdk\Dto\Offer\Attachment\AttachmentDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Mapper\CollectionMapperInterface;
use malpka32\InPostBuySdk\Mapper\ItemMapperInterface;

/**
 * @implements CollectionMapperInterface<AttachmentCollection>
 * @implements ItemMapperInterface<AttachmentDto>
 */
final class AttachmentMapper implements CollectionMapperInterface, ItemMapperInterface
{
    /**
     * @param array<string, mixed> $data { page: {...}, data: Attachment[] }
     */
    public function map(array $data): AttachmentCollection
    {
        $collection = new AttachmentCollection();
        $items = $data['data'] ?? [];
        if (!is_array($items)) {
            return $collection;
        }
        foreach ($items as $item) {
            /** @var array<string, mixed> $item */
            if (!$this->canProcess($item)) {
                continue;
            }
            $collection->add($this->mapItem($item));
        }
        return $collection;
    }

    public function canProcess(array $item): bool
    {
        return isset($item['id'], $item['name'], $item['url']);
    }

    public function mapItem(mixed $item): AttachmentDto
    {
        $item = is_array($item) ? $item : [];
        /** @var array<string, mixed> $item */
        return new AttachmentDto(
            id: ArrayHelper::asString($item['id'] ?? ''),
            name: ArrayHelper::asString($item['name'] ?? ''),
            attachmentType: ArrayHelper::asString($item['attachmentType'] ?? ''),
            createdAt: ArrayHelper::asString($item['createdAt'] ?? ''),
            url: ArrayHelper::asString($item['url'] ?? ''),
        );
    }
}
