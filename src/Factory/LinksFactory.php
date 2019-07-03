<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Factory;

use NoGlitchYo\JsonApiBuilder\Definition\Links;
use NoGlitchYo\JsonApiBuilder\Definition\LinksInterface;
use NoGlitchYo\JsonApiBuilder\Definition\ResourceObjectInterface;

class LinksFactory
{
    /**
     * @var string
     */
    private $apiUri;

    public function __construct(string $apiUri)
    {
        $this->apiUri = $apiUri;
    }

    public function create(ResourceObjectInterface $resourceObject): LinksInterface
    {
        return new Links($this->apiUri, $resourceObject);
    }
}
