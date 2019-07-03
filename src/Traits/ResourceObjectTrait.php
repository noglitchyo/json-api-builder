<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Traits;

trait ResourceObjectTrait
{
    public function getJsonAttributes(): array
    {
        return [];
    }

    public function getJsonApiRelationShips(): array
    {
        return [];
    }

    public function getJsonApiLinks(): array
    {
        return [];
    }

    public function getJsonApiMeta(): array
    {
        return [];
    }

    public function jsonSerialize(): array
    {
        /** @var \NoGlitchYo\JsonApiBuilder\Definition\ResourceObjectInterface $this */
        return [
            'attributes'    => $this->getJsonAttributes(),
            'id'            => $this->getJsonApiId(),
            'type'          => $this->getJsonApiType(),
            'relationships' => $this->getJsonApiRelationShips(),
            'meta'          => $this->getJsonApiMeta(),
            'links'         => $this->getJsonApiLinks(),
        ];
    }
}
