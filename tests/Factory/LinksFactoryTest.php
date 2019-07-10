<?php declare(strict_types=1);

namespace NoGlitchYo\JsonApiBuilder\Tests\Factory;

use NoGlitchYo\JsonApiBuilder\Factory\LinksFactory;
use NoGlitchYo\JsonApiBuilder\Tests\GetResourceObjectTrait;
use PHPUnit\Framework\TestCase;

class LinksFactoryTest extends TestCase
{
    use GetResourceObjectTrait;

    /**
     * @var LinksFactory
     */
    private $sut;

    protected function setUp(): void
    {
        $this->sut = new LinksFactory('//api.test/jsonapi');
    }

    public function testCreateReturnInstanceOfLinks(): void
    {
        $result = $this->sut->create(self::getResourceObject('json_api_type', 'json_api_id', [], [], [], []));

        $this->assertJsonStringEqualsJsonString(
            json_encode([
                'related' => [
                    'href' => '',
                    'meta' => [],
                ],
                'self' => '//api.test/jsonapi/json_api_type/json_api_id',
            ]),
            json_encode($result)
        );
    }
}
