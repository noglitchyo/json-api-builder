<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Tests\Processor;

use NoGlitchYo\JsonApiBuilder\Definition\ResourceObjectInterface;
use NoGlitchYo\JsonApiBuilder\Entity\ComputedResourceObject;
use NoGlitchYo\JsonApiBuilder\Processor\LinksProcessor;
use NoGlitchYo\JsonApiBuilder\Processor\RelationshipsProcessor;
use NoGlitchYo\JsonApiBuilder\Processor\ResourceObjectProcessor;
use NoGlitchYo\JsonApiBuilder\Tests\GetResourceObjectTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ResourceObjectProcessorTest extends TestCase
{
    use GetResourceObjectTrait;

    /**
     * @var RelationshipsProcessor|MockObject
     */
    private $relationshipsProcessorMock;

    /**
     * @var LinksProcessor|MockObject
     */
    private $linksProcessorMock;

    /**
     * @var ResourceObjectProcessor
     */
    private $sut;

    protected function setUp(): void
    {
        $this->linksProcessorMock         = $this->createMock(LinksProcessor::class);
        $this->relationshipsProcessorMock = $this->createMock(RelationshipsProcessor::class);

        $this->sut = new ResourceObjectProcessor($this->linksProcessorMock, $this->relationshipsProcessorMock);
    }

    /**
     * @dataProvider provideComputedObjects
     *
     * @param ResourceObjectInterface $resourceObject
     * @param array                   $expectedAttributes
     * @param array                   $expectedRelationships
     * @param array                   $expectedLinks
     * @param array                   $expectedMeta
     */
    public function testProcessReturnRelationships(
        ResourceObjectInterface $resourceObject,
        array $expectedAttributes,
        array $expectedRelationships,
        array $expectedLinks,
        array $expectedMeta
    ): void {
        $this->relationshipsProcessorMock
            ->expects($this->once())
            ->method('process')
            ->with($resourceObject)
            ->willReturn($expectedRelationships);

        $this->linksProcessorMock
            ->expects($this->once())
            ->method('process')
            ->with($resourceObject)
            ->willReturn($expectedLinks);

        $expected = new ComputedResourceObject(
            $resourceObject,
            $expectedRelationships,
            $expectedLinks
        );

        $result = $this->sut->process($resourceObject);

        $this->assertEquals($expected, $result);

        $expected = [
            'json_api_id',
            'json_api_type',
            $expectedAttributes,
            $expectedRelationships,
            $expectedLinks,
            $expectedMeta,
        ];

        $this->assertEquals($expected, [
            $result->getJsonApiId(),
            $result->getJsonApiType(),
            $result->getJsonAttributes(),
            $result->getJsonApiRelationships(),
            $result->getJsonApiLinks(),
            $result->getJsonApiMeta(),
        ]);
    }

    public function provideComputedObjects(): array
    {
        $attributes    = [
            'attribute_test_1' => 'value_1',
        ];
        $relationships = [
            'relationship_name',
        ];
        $links         = [
            ['self' => '/some/link'],
        ];
        $meta          = [
            'meta',
        ];

        return [
            [
                'resource' => static::getResourceObject(
                    'json_api_type',
                    'json_api_id',
                    $attributes,
                    $relationships,
                    $links,
                    $meta
                ),
                'attributes' => $attributes,
                'relationships' => $relationships,
                'links' => $links,
                'meta' => $meta,
            ],
        ];
    }
}
