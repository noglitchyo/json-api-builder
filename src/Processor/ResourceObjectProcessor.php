<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Processor;

use NoGlitchYo\JsonApiBuilder\Definition\ResourceObjectInterface;
use NoGlitchYo\JsonApiBuilder\Entity\ComputedResourceObject;

/**
 * Process a ResourceObjectInterface and computes relationships, links, etc...
 */
class ResourceObjectProcessor
{
    /**
     * @var LinksProcessor
     */
    private $linksProcessor;
    /**
     * @var RelationshipsProcessor
     */
    private $relationshipsProcessor;

    public function __construct(
        LinksProcessor $linksProcessor,
        RelationshipsProcessor $relationshipsProcessor
    ) {
        $this->linksProcessor = $linksProcessor;
        $this->relationshipsProcessor = $relationshipsProcessor;
    }

    public function process(ResourceObjectInterface $resourceObject): ResourceObjectInterface
    {
        return new ComputedResourceObject(
            $resourceObject,
            $this->relationshipsProcessor->process($resourceObject),
            $this->linksProcessor->process($resourceObject)
        );
    }
}
