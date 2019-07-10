<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Definition;

class Links implements LinksInterface
{
    /**
     * @var LinkInterface
     */
    private $related;

    /**
     * @var string
     */
    private $self;

    /**
     * @var string
     */
    private $apiUri;

    /**
     * @var ResourceObjectInterface
     */
    private $resourceObject;

    public function __construct(string $apiUri, ResourceObjectInterface $resourceObject)
    {
        $this->apiUri = $apiUri;
        $this->resourceObject = $resourceObject;
        $this->self = $this->apiUri . '/' .
            $this->resourceObject->getJsonApiType() . '/' .
            $this->resourceObject->getJsonApiId();
        $this->related = new Link();
    }

    public function getSelf(): string
    {
        return $this->self;
    }

    public function getRelated(): LinkInterface
    {
        return $this->related;
    }

    public function jsonSerialize(): array
    {
        return [
            'related' => $this->related,
            'self'    => $this->self,
        ];
    }
}
