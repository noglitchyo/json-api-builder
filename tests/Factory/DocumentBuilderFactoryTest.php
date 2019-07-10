<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Tests\Factory;

use NoGlitchYo\JsonApiBuilder\Builder\DocumentBuilder;
use NoGlitchYo\JsonApiBuilder\Factory\DocumentBuilderFactory;
use NoGlitchYo\JsonApiBuilder\Factory\DocumentFactory;
use NoGlitchYo\JsonApiBuilder\Processor\IncludedResourceProcessor;
use NoGlitchYo\JsonApiBuilder\Processor\ResourceObjectProcessor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DocumentBuilderFactoryTest extends TestCase
{
    /**
     * @var IncludedResourceProcessor|MockObject
     */
    private $includedResourceProcessorMock;

    /**
     * @var ResourceObjectProcessor|MockObject
     */
    private $resourceObjectProcessorMock;

    /**
     * @var DocumentBuilderFactory
     */
    private $sut;

    /**
     * @var DocumentFactory|MockObject
     */
    private $documentFactoryMock;

    protected function setUp(): void
    {
        $this->includedResourceProcessorMock = $this->createMock(IncludedResourceProcessor::class);
        $this->resourceObjectProcessorMock   = $this->createMock(ResourceObjectProcessor::class);
        $this->documentFactoryMock           = $this->createMock(DocumentFactory::class);

        $this->sut = new DocumentBuilderFactory(
            $this->includedResourceProcessorMock,
            $this->resourceObjectProcessorMock,
            $this->documentFactoryMock
        );
    }

    public function testCreateReturnInstanceOfLinks(): void
    {
        $result = $this->sut->create();

        $this->assertInstanceOf(DocumentBuilder::class, $result);
    }
}
