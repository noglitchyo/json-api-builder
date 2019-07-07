<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Definition;

use JsonSerializable;

interface ResourceObjectInterface extends JsonSerializable
{
    public function getJsonApiType(): string;

    public function getJsonApiId(): string;

    public function getJsonAttributes(): array;

    public function getJsonApiRelationShips(): array;

    public function getJsonApiLinks(): array;

    public function getJsonApiMeta(): array;
}
