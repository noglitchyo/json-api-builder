<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Definition;

interface LinkInterface
{
    public function getHref(): string;

    public function getMeta(): array;
}
