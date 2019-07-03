<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Definition;

class Link implements LinkInterface
{
    public function getHref(): string
    {
        return '';
    }

    public function getMeta(): array
    {
        return [];
    }
}
