<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Transport;

use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Decodes HTTP response to array.
 * Single responsibility: Response → array (DRY for endpoints).
 */
final class ResponseDecoder
{
    /**
     * @return array<string, mixed>
     */
    public function decodeToArray(ResponseInterface $response): array
    {
        $content = $response->getContent(false);
        if ($content === '') {
            return [];
        }
        $decoded = json_decode($content, true);
        return is_array($decoded) ? $decoded : [];
    }
}
