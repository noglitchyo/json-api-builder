<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Definition;

interface ErrorInterface
{
    public function getDetail(): string;

    public function getTitle(): string;

    public function getCode(): string;

    public function getStatus(): string;
}
