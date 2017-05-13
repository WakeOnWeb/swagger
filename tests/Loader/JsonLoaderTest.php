<?php

namespace WakeOnWeb\Swagger\Tests\Loader;

use org\bovigo\vfs\vfsStream as Stream;
use org\bovigo\vfs\vfsStreamFile as File;
use WakeOnWeb\Swagger\Loader\JsonLoader;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class JsonLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function testSupport()
    {
        $loader = new JsonLoader();

        $this->assertTrue($loader->supports('file.json'));
        $this->assertFalse($loader->supports('file.yaml'));
        $this->assertFalse($loader->supports('file.yml'));
    }

    /**
     * @test
     */
    public function testLoad()
    {
        $json = <<<JSON
{
    "swagger": "2.0"
}
JSON;

        $file = new File('file.json');
        $file->setContent($json);

        $directory = Stream::setup('tests');
        $directory->addChild($file);

        $loader = new JsonLoader();
        $spec = $loader->load('vfs://tests/file.json');

        $this->assertEquals(
            [
                'swagger' => '2.0',
            ],
            $spec
        );
    }
}