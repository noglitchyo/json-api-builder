<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Tests\Factory;

use NoGlitchYo\JsonApiBuilder\Definition\Document;
use NoGlitchYo\JsonApiBuilder\Factory\DocumentFactory;
use PHPUnit\Framework\TestCase;

class DocumentFactoryTest extends TestCase
{
    /**
     * @var DocumentFactory
     */
    private $sut;

    protected function setUp(): void
    {
        $this->sut = new DocumentFactory();
    }

    public function testCreateReturnInstanceOfLinks(): void
    {
        $result = $this->sut->create();

        $this->assertInstanceOf(Document::class, $result);
    }
}
