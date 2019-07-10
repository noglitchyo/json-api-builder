<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Tests\Processor;

use NoGlitchYo\JsonApiBuilder\Definition\ResourceObjectInterface;
use NoGlitchYo\JsonApiBuilder\Exception\UndefinedRelationshipDataException;
use NoGlitchYo\JsonApiBuilder\Processor\LinksProcessor;
use NoGlitchYo\JsonApiBuilder\Processor\RelationshipsProcessor;
use NoGlitchYo\JsonApiBuilder\Tests\GetResourceObjectTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RelationshipsProcessorTest extends TestCase
{
    use GetResourceObjectTrait;

    /**
     * @var LinksProcessor|MockObject
     */
    private $linksProcessorMock;

    /**
     * @var RelationshipsProcessor
     */
    private $sut;

    protected function setUp(): void
    {
        $this->linksProcessorMock = $this->createMock(LinksProcessor::class);

        $this->sut = new RelationshipsProcessor($this->linksProcessorMock);
    }

    public function testProcessThrowExceptionIfRelationshipDataIsNotDefinedInResourceAttributes(): void
    {
        $resource = static::getResourceObject(
            'json_api_type',
            'json_api_id',
            [
                'attribute_test_1' => 'value_1',
            ],
            ['relationship_name'],
            [],
            [
                'meta',
            ]
        );

        $this->expectException(UndefinedRelationshipDataException::class);

        $this->sut->process($resource);
    }

    /**
     * @dataProvider provideResourceObjects
     */
    public function testProcessReturnComputedResourceObjectWithComputedRelationships(
        ResourceObjectInterface $resourceObject,
        array $expectedRelationshipsLinks,
        array $expectedResult
    ): void {
        if (!empty($expectedRelationshipsLinks)) {
            $this->linksProcessorMock
                ->expects($this->once())
                ->method('processRelationship')
                ->with('relationship_name', $resourceObject)
                ->willReturn($expectedRelationshipsLinks);
        }

        $result = $this->sut->process($resourceObject);

        $this->assertEquals($expectedResult, $result);
    }

    public function provideResourceObjects(): array
    {
        $links = [
            ['self' => '/some/link'],
        ];

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
                'expected relationships links' => [],
                'expected result must be null' => [
                    'relationship_name' => [
                        'data' => null,
                    ],
                ],
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
                'expected relationships links' => [],
                'expected result must be an empty array' => [
                    'relationship_name' => [
                        'data' => [],
                    ],
                ],
            ],
            'resource with non-empty to-one relationships' => [
                'resource' => static::getResourceObject(
                    'json_api_type',
                    'json_api_id',
                    [
                        'attribute_test_1' => 'value_1',
                        'relationship_name' => static::getResourceObject(
                            'json_api_relation_type',
                            'json_api_relation_id',
                            [],
                            [],
                            $links,
                            []
                        ),
                    ],
                    ['relationship_name'],
                    $links,
                    [
                        'meta',
                    ]
                ),
                'expected relationships links' => $links,
                'expected result must be a single resource identifier object' => [
                    'relationship_name' => [
                        'data' => [
                            'type' => 'json_api_relation_type',
                            'id' => 'json_api_relation_id',
                        ],
                        'links' => $links,
                    ],
                ],
            ],
            'resource with non-empty to-many relationships' => [
                'resource' => static::getResourceObject(
                    'json_api_type',
                    'json_api_id',
                    [
                        'attribute_test_1' => 'value_1',
                        'relationship_name' => [
                            static::getResourceObject(
                                'json_api_relation_type',
                                'json_api_relation_id',
                                [],
                                [],
                                $links,
                                []
                            ),
                        ],
                    ],
                    ['relationship_name'],
                    $links,
                    ['meta']
                ),
                'expected relationships links' => $links,
                'expected result must be an array of resource identifier objects' => [
                    'relationship_name' => [
                        'data' => [
                            [
                                'type' => 'json_api_relation_type',
                                'id' => 'json_api_relation_id',
                            ],
                        ],
                        'links' => $links,
                    ],
                ],
            ],
        ];
    }
}
