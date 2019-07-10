<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Definition;

use JsonSerializable;

/**
 * @codeCoverageIgnore
 */
interface LinkInterface extends JsonSerializable
{
    public function getHref(): string;

    public function getMeta(): array;
}
