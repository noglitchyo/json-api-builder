<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Definition;

use JsonSerializable;

/**
 * Immutable JSON:API document
 *
 * Interface DocumentInterface
 * @package NoGlitchYo\JsonApiBuilder
 */
interface DocumentInterface extends JsonSerializable
{
    public function withMeta($meta): self;

    public function withErrors(array $errors): self;

    public function withData($data): self;

    public function withIncluded($included): self;
}
