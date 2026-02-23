<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Offer\PostSale;

use malpka32\InPostBuySdk\Dto\Offer\PostSaleDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Mapper\SingleItemMapperInterface;

/**
 * @implements SingleItemMapperInterface<PostSaleDto>
 */
final class OfferPostSaleSingleMapper implements SingleItemMapperInterface
{
    public function map(mixed $data): ?PostSaleDto
    {
        if (!is_array($data)) {
            return null;
        }
        /** @var array<string, mixed> $data */
        $returnDesc = $this->extractPolicyDescription($data, 'returnPolicy');
        $complaintDesc = $this->extractPolicyDescription($data, 'complaintPolicy');

        if (empty($returnDesc) && empty($complaintDesc)) {
            return null;
        }

        return new PostSaleDto(
            returnPolicyDescription: !empty($returnDesc) ? $returnDesc : null,
            complaintPolicyDescription: !empty($complaintDesc) ? $complaintDesc : null,
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private function extractPolicyDescription(array $data, string $policyKey): ?string
    {
        $policy = $data[$policyKey] ?? null;
        if (!is_array($policy) || !isset($policy['description'])) {
            return null;
        }

        $description = ArrayHelper::asString($policy['description']);
        return !empty($description) ? $description : null;
    }
}
