<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Factory;

use NoGlitchYo\JsonApiBuilder\Definition\Document;
use NoGlitchYo\JsonApiBuilder\Definition\DocumentInterface;

class DocumentFactory
{
    public function create(): DocumentInterface
    {
        return new Document();
    }
}
