<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Tests\Processor;

use NoGlitchYo\JsonApiBuilder\Definition\ResourceObjectInterface;
use NoGlitchYo\JsonApiBuilder\Exception\UndefinedRelationshipDataException;
use NoGlitchYo\JsonApiBuilder\Exception\UndefinedRelationshipException;
use NoGlitchYo\JsonApiBuilder\Processor\IncludedResourceProcessor;
use NoGlitchYo\JsonApiBuilder\Processor\ResourceObjectProcessor;
use NoGlitchYo\JsonApiBuilder\Tests\GetResourceObjectTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class IncludedResourceProcessorTest extends TestCase
{
    use GetResourceObjectTrait;

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
}
