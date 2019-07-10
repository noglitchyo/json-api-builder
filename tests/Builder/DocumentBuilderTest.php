<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Tests\Builder;

use NoGlitchYo\JsonApiBuilder\Builder\DocumentBuilder;
use NoGlitchYo\JsonApiBuilder\Definition\Document;
use NoGlitchYo\JsonApiBuilder\Definition\DocumentInterface;
use NoGlitchYo\JsonApiBuilder\Definition\ErrorInterface;
use NoGlitchYo\JsonApiBuilder\Definition\ResourceObjectInterface;
use NoGlitchYo\JsonApiBuilder\Factory\DocumentFactory;
use NoGlitchYo\JsonApiBuilder\Processor\IncludedResourceProcessor;
use NoGlitchYo\JsonApiBuilder\Processor\ResourceObjectProcessor;
use NoGlitchYo\JsonApiBuilder\Traits\ResourceObjectTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DocumentBuilderTest extends TestCase
{
    /**
     * @var ResourceObjectInterface
     */
    private static $resourceObject;

    /**
     * @var ResourceObjectInterface
     */
    private static $relationshipObject;

    /**
     * @var ErrorInterface
     */
    private static $errorObject;

    /**
     * @var MockObject|DocumentFactory
     */
    private $documentFactoryMock;

    /**
     * @var MockObject|ResourceObjectProcessor
     */
    private $resourceObjectProcessorMock;

    /**
     * @var MockObject|IncludedResourceProcessor
     */
    private $includedResourceProcessorMock;

    /**
     * @var DocumentBuilder
     */
    private $sut;

    /**
     * @var array
     */
    private $includes;

    public static function setUpBeforeClass(): void
    {
        static::$relationshipObject = static::getRelationshipJsonApiObject();
        static::$resourceObject = static::getResourceObject(static::$relationshipObject);
        static::$errorObject = static::getErrorObject();
    }

    protected function setUp(): void
    {
        $this->includes = [
            'resource_type_1',
            'relationship_identifier',
        ];

        $this->includedResourceProcessorMock = $this->createMock(IncludedResourceProcessor::class);
        $this->resourceObjectProcessorMock = $this->createMock(ResourceObjectProcessor::class);
        $this->documentFactoryMock = $this->createMock(DocumentFactory::class);

        $this->sut = new DocumentBuilder(
            $this->includedResourceProcessorMock,
            $this->resourceObjectProcessorMock,
            $this->documentFactoryMock
        );
    }

    public function testAddResourceObjectsReturnDocumentBuilder(): void
    {
        $documentBuilder = $this->sut->addResourceObjects([static::$resourceObject]);

        $this->assertSame($documentBuilder, $this->sut);
    }

    public function testAddIncludesReturnDocumentBuilder(): void
    {
        $documentBuilder = $this->sut->addIncludes($this->includes);

        $this->assertSame($documentBuilder, $this->sut);
    }

    public function testBuildReturnDocumentFromAllGivenBuildParameters(): void
    {
        $resourceObjects = [static::$resourceObject];
        $expectedIncluded = [static::$relationshipObject];
        $expectedMeta = [['meta_test' => 'value']];
        $expectedDocumentErrors = [
            [
                'title' => static::$errorObject->getTitle(),
                'detail' => static::$errorObject->getDetail(),
                'code' => static::$errorObject->getCode(),
                'status' => static::$errorObject->getStatus(),
            ]
        ];

        $this->sut = $this->sut
            ->addResourceObjects($resourceObjects)
            ->addIncludes($this->includes)
            ->addMeta(['meta_test' => 'value'])
            ->addErrors([static::$errorObject]);

        $newDocument = new Document();

        $this->documentFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($newDocument);

        $this->resourceObjectProcessorMock
            ->expects($this->once())
            ->method('process')
            ->willReturn(static::$resourceObject);

        $this->includedResourceProcessorMock
            ->expects($this->once())
            ->method('process')
            ->with(static::$resourceObject)
            ->willReturn($expectedIncluded);

        $document = $this->sut->build();

        $this->assertInstanceOf(DocumentInterface::class, $document);

        $this->assertEquals($resourceObjects, $document->getData());
        $this->assertEquals($expectedIncluded, $document->getIncluded());
        $this->assertEquals($expectedDocumentErrors, $document->getErrors());
        $this->assertEquals($expectedMeta, $document->getMeta());

        $this->assertJsonStringEqualsJsonString(
            json_encode([
                'meta' => $expectedMeta,
                'errors' => $expectedDocumentErrors,
                'data' => $resourceObjects,
                'included' => $expectedIncluded
            ]),
            (string)$document
        );
    }

    public static function getResourceObject(ResourceObjectInterface $relationship): ResourceObjectInterface
    {
        return new class($relationship) implements ResourceObjectInterface
        {
            use ResourceObjectTrait;

            /**
             * @var ResourceObjectInterface
             */
            private $relationshipResourceObject;

            public function __construct(ResourceObjectInterface $relationship)
            {
                $this->relationshipResourceObject = $relationship;
            }

            public function getJsonAttributes(): array
            {
                return [
                    'attribute_key_name' => $this->relationshipResourceObject,
                ];
            }

            public function getJsonApiRelationships(): array
            {
                return [
                    'relationship_identifier' => 'attribute_key_name',
                ];
            }

            public function getJsonApiType(): string
            {
                return 'resource_type_1';
            }

            public function getJsonApiId(): string
            {
                return 'json_api_id';
            }
        };
    }

    private static function getRelationshipJsonApiObject(): ResourceObjectInterface
    {
        return new class implements ResourceObjectInterface
        {
            use ResourceObjectTrait;

            public function getJsonApiType(): string
            {
                return 'relationship_json_api_type';
            }

            public function getJsonApiId(): string
            {
                return 'relationship_json_api_id';
            }
        };
    }

    private static function getErrorObject(): ErrorInterface
    {
        return new class implements ErrorInterface
        {
            public function getDetail(): string
            {
                return 'detail';
            }

            public function getTitle(): string
            {
                return 'title';
            }

            public function getCode(): string
            {
                return 'code';
            }

            public function getStatus(): string
            {
                return 'status';
            }
        };
    }
}
