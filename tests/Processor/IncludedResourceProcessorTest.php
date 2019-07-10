<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Tests\Processor;

use NoGlitchYo\JsonApiBuilder\Definition\ResourceObjectInterface;
use NoGlitchYo\JsonApiBuilder\Exception\UndefinedRelationshipDataException;
use NoGlitchYo\JsonApiBuilder\Exception\UndefinedRelationshipException;
use NoGlitchYo\JsonApiBuilder\Processor\IncludedResourceProcessor;
use NoGlitchYo\JsonApiBuilder\Processor\ResourceObjectProcessor;
use NoGlitchYo\JsonApiBuilder\Traits\ResourceObjectTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class IncludedResourceProcessorTest extends TestCase
{
    /**
     * @var ResourceObjectProcessor|MockObject
     */
    private $resourceObjectProcessorMock;

    /**
     * @var IncludedResourceProcessor
     */
    private $sut;

    protected function setUp(): void
    {
        $this->resourceObjectProcessorMock = $this->createMock(ResourceObjectProcessor::class);

        $this->sut = new IncludedResourceProcessor($this->resourceObjectProcessorMock);
    }

    public function testProcessThrowExceptionIfRelationshipIsNotDefined(): void
    {
        $resource = static::getResourceObject('json_api_type', 'json_api_id', [], [], [], []);

        $this->expectException(UndefinedRelationshipException::class);

        $this->sut->process($resource, ['relationship_name']);
    }

    public function testProcessThrowExceptionIfRelationshipDataIsNotDefinedInResourceAttributes(): void
    {
        $resource = static::getResourceObject('json_api_type', 'json_api_id', [], ['relationship_name'], [], []);

        $this->expectException(UndefinedRelationshipDataException::class);

        $this->sut->process($resource, ['relationship_name']);
    }

    /**
     * @dataProvider provideResourceObjects
     */
    public function testProcessReturnIncludedResourceObjects(
        ResourceObjectInterface $resourceObject,
        array $include,
        ResourceObjectInterface $relationshipResourceObject,
        $expectedResult
    ): void {

        $this->resourceObjectProcessorMock->expects($this->any())
            ->method('process')
            ->with($relationshipResourceObject)
            ->willReturn($relationshipResourceObject);

        $result = $this->sut->process($resourceObject, $include);

        $this->assertSame($expectedResult, $result);
    }

    public function provideResourceObjects(): array
    {
        $links = [
            ['self' => '/some/link'],
        ];

        $relationshipResourceObject = static::getResourceObject(
            'json_api_relation_type',
            'json_api_relation_id',
            [],
            [],
            [],
            []
        );

        return [
            'resource with empty to-one relationships' => [
                'resource' => static::getResourceObject(
                    'json_api_type',
                    'json_api_id',
                    [
                        'attribute_test_1' => 'value_1',
                        'relationship_name' => null,
                    ],
                    ['relationship_name'],
                    $links,
                    [
                        'meta',
                    ]
                ),
                'include' => ['relationship_name'],
                'relationship resource object' => $relationshipResourceObject,
                'expected result must be null' => [],
            ],
            'resource with empty to-many relationships' => [
                'resource' => static::getResourceObject(
                    'json_api_type',
                    'json_api_id',
                    [
                        'attribute_test_1' => 'value_1',
                        'relationship_name' => [],
                    ],
                    ['relationship_name'],
                    $links,
                    [
                        'meta',
                    ]
                ),
                'include' => ['relationship_name'],
                'relationship resource object' => $relationshipResourceObject,
                'expected result must be an empty array' => [],
            ],
            'resource with non-empty to-one relationships' => [
                'resource' => static::getResourceObject(
                    'json_api_type',
                    'json_api_id',
                    [
                        'attribute_test_1' => 'value_1',
                        'relationship_name' => $relationshipResourceObject,
                    ],
                    ['relationship_name'],
                    $links,
                    [
                        'meta',
                    ]
                ),
                'include' => ['relationship_name'],
                'relationship resource object' => $relationshipResourceObject,
                'expected result must be a single resource identifier object' => [
                    $relationshipResourceObject,
                ],
            ],
            'resource with non-empty to-many relationships' => [
                'resource' => static::getResourceObject(
                    'json_api_type',
                    'json_api_id',
                    [
                        'attribute_test_1' => 'value_1',
                        'relationship_name' => [$relationshipResourceObject],
                    ],
                    ['relationship_name'],
                    $links,
                    ['meta']
                ),
                'include' => ['relationship_name'],
                'relationship resource object' => $relationshipResourceObject,
                'expected result must be an array of resource identifier objects' => [
                    $relationshipResourceObject,
                ],
            ],
        ];
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
