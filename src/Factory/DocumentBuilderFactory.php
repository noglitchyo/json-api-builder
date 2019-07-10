<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Factory;

use NoGlitchYo\JsonApiBuilder\Builder\DocumentBuilder;
use NoGlitchYo\JsonApiBuilder\Processor\IncludedResourceProcessor;
use NoGlitchYo\JsonApiBuilder\Processor\ResourceObjectProcessor;

class DocumentBuilderFactory
{
    /**
     * @var IncludedResourceProcessor
     */
    private $includedResourceProcessor;

    /**
     * @var ResourceObjectProcessor
     */
    private $resourceObjectProcessor;

    /**
     * @var DocumentFactory
     */
    private $documentFactory;

    public function __construct(
        IncludedResourceProcessor $includedResourceProcessor,
        ResourceObjectProcessor $resourceObjectProcessor,
        DocumentFactory $documentFactory
    ) {
        $this->includedResourceProcessor = $includedResourceProcessor;
        $this->resourceObjectProcessor   = $resourceObjectProcessor;
        $this->documentFactory           = $documentFactory;
    }

    public function create(): DocumentBuilder
    {
        return new DocumentBuilder(
            $this->includedResourceProcessor,
            $this->resourceObjectProcessor,
            $this->documentFactory
        );
    }
}
