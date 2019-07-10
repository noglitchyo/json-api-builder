<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Tests\Processor;

use NoGlitchYo\JsonApiBuilder\Definition\Link;
use NoGlitchYo\JsonApiBuilder\Definition\Links;
use NoGlitchYo\JsonApiBuilder\Definition\ResourceObjectInterface;
use NoGlitchYo\JsonApiBuilder\Factory\LinksFactory;
use NoGlitchYo\JsonApiBuilder\Processor\LinksProcessor;
use NoGlitchYo\JsonApiBuilder\Traits\ResourceObjectTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class LinksProcessorTest extends TestCase
{
    /**
     * @var LinksFactory|MockObject
     */
    private $linksFactoryMock;

    /**
     * @var LinksProcessor
     */
    private $sut;

    protected function setUp(): void
    {
        $this->linksFactoryMock = $this->createMock(LinksFactory::class);

        $this->sut = new LinksProcessor($this->linksFactoryMock);
    }

    public function testProcessReturnComputedLinks(): void
    {
        $resourceObject = static::getResourceObject('json_api_type', 'json_api_id', [], [], [], []);

        $links = new Links('//api.test/jsonapi', $resourceObject);

        $expectedSelf    = '//api.test/jsonapi/json_api_type/json_api_id';
        $expectedRelated = new Link();

        $expected = [
            'related' => $expectedRelated,
            'self' => $expectedSelf,
        ];

        $this->linksFactoryMock->expects($this->once())
            ->method('create')
            ->with($resourceObject)
            ->willReturn($links);

        $result = $this->sut->process($resourceObject);

        $this->assertEquals($expected, $result);
    }

    public function testProcessRelationshipsReturnComputedLinks(): void
    {
        $resourceObject = static::getResourceObject('json_api_type', 'json_api_id', [], [], [], []);

        $links = new Links('//api.test/jsonapi', $resourceObject);

        $expectedSelf    = '//api.test/jsonapi/json_api_type/json_api_id/relationship_name';
        $expectedRelated = '//api.test/jsonapi/json_api_type/json_api_id/relationships/relationship_name';

        $expected = [
            'related' => $expectedRelated,
            'self' => $expectedSelf,
        ];

        $this->linksFactoryMock->expects($this->once())
            ->method('create')
            ->with($resourceObject)
            ->willReturn($links);

        $result = $this->sut->processRelationship('relationship_name', $resourceObject);

        $this->assertEquals($expected, $result);
    }


    public static function getResourceObject(
        string $type,
        string $id,
        array $attributes,
        array $relationships,
        array $links,
        array $meta
    ): ResourceObjectInterface {
        return new class($type, $id, $attributes, $relationships, $links, $meta) implements ResourceObjectInterface
        {
            use ResourceObjectTrait;

            /**
             * @var array
             */
            private $relationships;

            /**
             * @var array
             */
            private $links;

            /**
             * @var array
             */
            private $meta;

            /**
             * @var array
             */
            private $attributes;
            /**
             * @var string
             */
            private $type;
            /**
             * @var string
             */
            private $id;

            public function __construct(
                string $type,
                string $id,
                array $attributes,
                array $relationships,
                array $links,
                array $meta
            ) {
                $this->attributes    = $attributes;
                $this->relationships = $relationships;
                $this->links         = $links;
                $this->meta          = $meta;
                $this->type          = $type;
                $this->id            = $id;
            }

            public function getJsonAttributes(): array
            {
                return $this->attributes;
            }

            public function getJsonApiRelationships(): array
            {
                return $this->relationships;
            }

            public function getJsonApiLinks(): array
            {
                return $this->links;
            }

            public function getJsonApiMeta(): array
            {
                return $this->meta;
            }

            public function getJsonApiType(): string
            {
                return $this->type;
            }

            public function getJsonApiId(): string
            {
                return $this->id;
            }
        };
    }
}
