<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Tests;

use NoGlitchYo\JsonApiBuilder\Definition\ResourceObjectInterface;
use NoGlitchYo\JsonApiBuilder\Traits\ResourceObjectTrait;

trait GetResourceObjectTrait
{
    public static function getResourceObject(
        string $type,
        string $id,
        array $attributes = [],
        array $relationships = [],
        array $links = [],
        array $meta = []
    ): ResourceObjectInterface {
        return new class($type, $id, $attributes, $relationships, $links, $meta) implements ResourceObjectInterface
        {
            use ResourceObjectTrait;

            /**
             * @var array
             */
            private $relationships;

            /**
             * @var array
             */
            private $links;

            /**
             * @var array
             */
            private $meta;

            /**
             * @var array
             */
            private $attributes;
            /**
             * @var string
             */
            private $type;
            /**
             * @var string
             */
            private $id;

            public function __construct(
                string $type,
                string $id,
                array $attributes,
                array $relationships,
                array $links,
                array $meta
            ) {
                $this->attributes    = $attributes;
                $this->relationships = $relationships;
                $this->links         = $links;
                $this->meta          = $meta;
                $this->type          = $type;
                $this->id            = $id;
            }

            public function getJsonAttributes(): array
            {
                return $this->attributes;
            }

            public function getJsonApiRelationships(): array
            {
                return $this->relationships;
            }

            public function getJsonApiLinks(): array
            {
                return $this->links;
            }

            public function getJsonApiMeta(): array
            {
                return $this->meta;
            }

            public function getJsonApiType(): string
            {
                return $this->type;
            }

            public function getJsonApiId(): string
            {
                return $this->id;
            }
        };
    }
}
