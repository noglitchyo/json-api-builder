<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Builder;

use NoGlitchYo\JsonApiBuilder\Definition\DocumentInterface;
use NoGlitchYo\JsonApiBuilder\Definition\ErrorInterface;
use NoGlitchYo\JsonApiBuilder\Definition\ResourceObjectInterface;
use NoGlitchYo\JsonApiBuilder\Factory\DocumentFactory;
use NoGlitchYo\JsonApiBuilder\Processor\IncludedResourceProcessor;
use NoGlitchYo\JsonApiBuilder\Processor\ResourceObjectProcessor;

class DocumentBuilder
{
    /**
     * @var ResourceObjectInterface[]
     */
    private $resourceObjects = [];

    /**
     * @var string[]
     */
    private $includes = [];

    /**
     * @var ErrorInterface[]
     */
    private $errorObjects = [];

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

    /**
     * @var array
     */
    private $metaObjects;

    public function __construct(
        IncludedResourceProcessor $includedResourceProcessor,
        ResourceObjectProcessor $resourceObjectProcessor,
        DocumentFactory $documentFactory
    ) {
        $this->includedResourceProcessor = $includedResourceProcessor;
        $this->documentFactory           = $documentFactory;
        $this->resourceObjectProcessor   = $resourceObjectProcessor;
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

    public function addErrors(array $errors): self
    {
        foreach ($errors as $error) {
            $this->addError($error);
        }

        return $this;
    }

    public function addError(ErrorInterface $error): self
    {
        $this->errorObjects[] = $error;

        return $this;
    }

    public function addMeta(array $meta): self
    {
        $this->metaObjects[] = $meta;

        return $this;
    }

    public function build(): DocumentInterface
    {
        $document = $this->documentFactory->create();

        if (!empty($this->resourceObjects)) {
            $document = $this->buildData($document);
        }

        if (!empty($this->includes)) {
            $document = $this->buildIncludes($document);
        }

        if (!empty($this->errorObjects)) {
            $document = $this->buildErrorObjects($document);
        }

        if (!empty($this->metaObjects)) {
            $document = $this->buildMetaObject($document);
        }

        return $document;
    }

    private function buildData(DocumentInterface $document): DocumentInterface
    {
        $computedResourceObjects = [];
        foreach ($this->resourceObjects as $resourceObject) {
            $computedResourceObjects[] = $this->resourceObjectProcessor->process($resourceObject);
        }
        return $document->withData($computedResourceObjects);
    }

    private function buildIncludes(DocumentInterface $document): DocumentInterface
    {
        $included = [];
        foreach ($this->resourceObjects as $resourceObject) {
            $included = $included + $this->includedResourceProcessor->process($resourceObject, $this->includes);
        }
        return $document->withIncluded($included);
    }

    private function buildErrorObjects(DocumentInterface $document): DocumentInterface
    {
        $errors = [];
        foreach ($this->errorObjects as $errorObject) {
            $errors[] = [
                'title' => $errorObject->getTitle(),
                'detail' => $errorObject->getDetail(),
                'code' => $errorObject->getCode(),
                'status' => $errorObject->getStatus(),
            ];
        }
        return $document->withErrors($errors);
    }

    private function buildMetaObject(DocumentInterface $document): DocumentInterface
    {
        return $document->withMeta($this->metaObjects);
    }
}
