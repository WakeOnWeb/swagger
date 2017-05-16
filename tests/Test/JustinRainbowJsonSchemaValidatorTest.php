<?php

namespace WakeOnWeb\Component\Swagger\Tests;

use WakeOnWeb\Component\Swagger\Specification\Definitions;
use WakeOnWeb\Component\Swagger\Specification\Schema;
use WakeOnWeb\Component\Swagger\Test\JustinRainbowJsonSchemaValidator;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class JustinRainbowJsonSchemaValidatorTest extends \PHPUnit_Framework_TestCase
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
        $validator->validateContent($this->buildSchema($schema), $content);
    }

    /**
     * @test
     */
    public function testValidateContentThatUsesReferences()
    {
        $schema = <<<'JSON'
{
    "$ref": "#/definitions/node",
    "definitions": {
        "node": {
            "type": "object",
            "properties": {
                "name": {
                    "type": "string"
                },
                "children": {
                    "type": "array",
                    "items": {
                        "$ref": "#/definitions/node"
                    }
                }
            },
            "required": ["name", "children"]
        }
    }
}
JSON;

        $content = <<<JSON
{
    "name": "WakeOnWeb",
    "children": [
        {
            "name": "Component",
            "children": [
                {
                    "name": "Swagger",
                    "children": []
                }
            ]
        }
    ]
}
JSON;

        $validator = new JustinRainbowJsonSchemaValidator();
        $validator->validateContent($this->buildSchema($schema), $content);
    }

    /**
     * @test
     * @expectedException \WakeOnWeb\Component\Swagger\Test\Exception\ContentValidatorException
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
        $validator->validateContent($this->buildSchema($schema), $content);
    }

    /**
     * @param string $schema
     *
     * @return Schema
     */
    private function buildSchema($schema)
    {
        return new Schema(json_decode($schema, true), new Definitions(), null, null, null, null, null);
    }
}
