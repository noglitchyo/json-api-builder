<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Factory;

use NoGlitchYo\JsonApiBuilder\Builder\DocumentBuilder;
use NoGlitchYo\JsonApiBuilder\Processor\ResourceObjectProcessor;
use NoGlitchYo\JsonApiBuilder\Processor\IncludedResourceProcessor;

class DocumentBuilderFactory
{
    /**
     * @var IncludedResourceProcessor
     */
    private $jsonApiSerializer;
    /**
     * @var ResourceObjectProcessor
     */
    private $resourceObjectProcessor;
    /**
     * @var DocumentFactory
     */
    private $documentFactory;

    public function __construct(
        IncludedResourceProcessor $jsonApiSerializer,
        ResourceObjectProcessor $resourceObjectProcessor,
        DocumentFactory $documentFactory
    ) {
        $this->jsonApiSerializer = $jsonApiSerializer;
        $this->resourceObjectProcessor = $resourceObjectProcessor;
        $this->documentFactory = $documentFactory;
    }

    public function create(): DocumentBuilder
    {
        return new DocumentBuilder($this->jsonApiSerializer, $this->resourceObjectProcessor, $this->documentFactory);
    }
}
