<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Tests\Definition;

use InvalidArgumentException;
use JsonSerializable;
use NoGlitchYo\JsonApiBuilder\Definition\Document;
use PHPUnit\Framework\TestCase;
use stdClass;

class DocumentTest extends TestCase
{
    public function testWithDataThrowExceptionIfDataIsNotInstanceOfJsonSerializableWhenObjectIsProvided()
    {
        $document = new Document();

        $this->expectException(InvalidArgumentException::class);

        $document->withData(new stdClass());
    }

    public function testWithDataDoNotThrowExceptionIfDataIsInstanceOfJsonSerializableWhenObjectIsProvided()
    {
        $document = new Document();

        $document = $document->withData(new class implements JsonSerializable
        {
            public function jsonSerialize()
            {
                return [];
            }
        });

        $this->assertInstanceOf(Document::class, $document);
    }
}
