<?php

namespace WakeOnWeb\Swagger\Tests;

use WakeOnWeb\Swagger\Test\JustinRainbowJsonSchemaValidator;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class JsonSchemaValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function testSupport()
    {
        $validator = new JustinRainbowJsonSchemaValidator();

        $this->assertTrue($validator->support('application/json'));
        $this->assertFalse($validator->support('application/xml'));
    }

    /**
     * @test
     */
    public function testValidateContent()
    {
        $schema = <<<JSON
{
    "type": "object",
    "properties": {
        "id": {
            "type": "integer"
        }
    }
}
JSON;

        $content = <<<JSON
{
    "id": 1
}
JSON;

        $validator = new JustinRainbowJsonSchemaValidator();
        $validator->validateContent($schema, $content);
    }

    /**
     * @test
     * @expectedException \WakeOnWeb\Swagger\Test\Exception\ContentValidatorException
     */
    public function testValidateContentThrowsAnExceptionWhenInvalid()
    {
        $schema = <<<JSON
{
    "type": "object",
    "properties": {
        "id": {
            "type": "integer"
        }
    }
}
JSON;

        $content = <<<JSON
{
    "id": null
}
JSON;

        $validator = new JustinRainbowJsonSchemaValidator();
        $validator->validateContent($schema, $content);
    }
}
