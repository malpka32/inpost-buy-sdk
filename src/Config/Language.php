<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Config;

/**
 * Supported languages for Accept-Language header (ISO 639-1).
 * InPost Buy API supports: pl (Polish), en (English).
 */
enum Language: string
{
    case Polish = 'pl';
    case English = 'en';
}
