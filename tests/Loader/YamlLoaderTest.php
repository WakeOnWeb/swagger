<?php

namespace WakeOnWeb\Component\Swagger\Tests\Loader;

use org\bovigo\vfs\vfsStream as Stream;
use org\bovigo\vfs\vfsStreamFile as File;
use WakeOnWeb\Component\Swagger\Loader\YamlLoader;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class YamlLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function testSupport()
    {
        $loader = new YamlLoader();

        $this->assertFalse($loader->supports('file.json'));
        $this->assertTrue($loader->supports('file.yaml'));
        $this->assertTrue($loader->supports('file.yml'));
    }

    /**
     * @test
     */
    public function testLoad()
    {
        $json = <<<JSON
swagger: 2.0
JSON;

        $file = new File('file.yml');
        $file->setContent($json);

        $directory = Stream::setup('tests');
        $directory->addChild($file);

        $loader = new YamlLoader();
        $spec = $loader->load('vfs://tests/file.yml');

        $this->assertEquals(
            [
                'swagger' => '2.0',
            ],
            $spec
        );
    }
}
