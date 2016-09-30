<?php

namespace UCS\Swagger\Tests;

use UCS\Swagger\Specification\Response;
use UCS\Swagger\Specification\Schema;
use UCS\Swagger\Test\JustinRainbowJsonSchemaValidator;

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
        $validator->validateContent($this->buildResponse($schema), $content);
    }

    /**
     * @test
     * @expectedException \UCS\Swagger\Test\Exception\ContentValidatorException
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
        $validator->validateContent($this->buildResponse($schema), $content);
    }

    /**
     * @param string $schema
     *
     * @return Response
     */
    private function buildResponse($schema)
    {
        return new Response(null, new Schema(json_decode($schema, true), null, null, null, null, null));
    }
}