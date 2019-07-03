<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Processor;

use NoGlitchYo\JsonApiBuilder\Factory\LinksFactory;
use NoGlitchYo\JsonApiBuilder\Definition\ResourceObjectInterface;

class LinksProcessor
{
    /**
     * @var LinksFactory
     */
    private $linksFactory;

    public function __construct(LinksFactory $linksFactory)
    {
        $this->linksFactory = $linksFactory;
    }

    public function process(ResourceObjectInterface $resourceObject): array
    {
        $links = $this->linksFactory->create($resourceObject);

        return [
            'related' => $links->getRelated(),
            'self' => $links->getSelf(),
        ];
    }

    public function processRelationship(string $relationshipName, ResourceObjectInterface $resourceObject): array
    {
        $links = $this->linksFactory->create($resourceObject);

        return [
            'related' => $links->getSelf() . '/relationships/' . $relationshipName,
            'self' => $links->getSelf() . '/' . $relationshipName,
        ];
    }
}
