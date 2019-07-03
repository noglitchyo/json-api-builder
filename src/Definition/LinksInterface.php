<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Definition;

use JsonSerializable;

interface LinksInterface extends JsonSerializable
{
    public function getSelf(): string;

    public function getRelated(): LinkInterface;
}
