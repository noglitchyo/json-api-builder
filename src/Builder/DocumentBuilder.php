<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Builder;

use NoGlitchYo\JsonApiBuilder\Definition\DocumentInterface;
use NoGlitchYo\JsonApiBuilder\Factory\DocumentFactory;
use NoGlitchYo\JsonApiBuilder\Processor\ResourceObjectProcessor;
use NoGlitchYo\JsonApiBuilder\Definition\ResourceObjectInterface;
use NoGlitchYo\JsonApiBuilder\Processor\IncludedResourceProcessor;

class DocumentBuilder
{
    /**
     * @var ResourceObjectInterface[]
     */
    private $resourceObjects;

    /**
     * @var string[]
     */
    private $includes;

    /**
     * @var IncludedResourceProcessor
     */
    private $includedResourceProcessor;

    /**
     * @var DocumentFactory
     */
    private $documentFactory;

    /**
     * @var ResourceObjectProcessor
     */
    private $resourceObjectProcessor;

    public function __construct(
        IncludedResourceProcessor $includedResourceProcessor,
        ResourceObjectProcessor $resourceObjectProcessor,
        DocumentFactory $documentFactory
    )
    {
        $this->includedResourceProcessor = $includedResourceProcessor;
        $this->documentFactory = $documentFactory;
        $this->resourceObjectProcessor = $resourceObjectProcessor;
    }

    public function addResourceObject(ResourceObjectInterface $resourceObject): self
    {
        $this->resourceObjects[] = $resourceObject;

        return $this;
    }

    public function addResourceObjects(array $resourceObjects): self
    {
        foreach ($resourceObjects as $resourceObject) {
            $this->addResourceObject($resourceObject);
        }

        return $this;
    }

    public function addInclude(string $include): self
    {
        $this->includes[] = $include;

        return $this;
    }

    public function addIncludes(array $includes): self
    {
        foreach ($includes as $include) {
            $this->addInclude($include);
        }

        return $this;
    }

    public function build(): DocumentInterface
    {
        $document = $this->documentFactory->create();

        $computedResourceObjects = [];

        foreach ($this->resourceObjects as $resourceObject) {
            $computedResourceObjects[] = $this->resourceObjectProcessor->process($resourceObject);
        }

        $document = $document->withData($computedResourceObjects);

        if (!empty($this->includes)) {
            $included = [];
            foreach ($this->resourceObjects as $resourceObject) {
                $included[] = $this->includedResourceProcessor->process($resourceObject, $this->includes);
            }
            $document = $document->withIncluded($included);
        }

        return $document;
    }
}
