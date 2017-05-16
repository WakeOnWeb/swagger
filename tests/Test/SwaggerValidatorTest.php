<?php

namespace WakeOnWeb\Component\Swagger\Tests;

use WakeOnWeb\Component\Swagger\Specification\PathItem;
use WakeOnWeb\Component\Swagger\Specification\Swagger;
use WakeOnWeb\Component\Swagger\SwaggerFactory;
use WakeOnWeb\Component\Swagger\Test\Response\ResponseInterface;
use WakeOnWeb\Component\Swagger\Test\SwaggerValidator;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class SwaggerValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function testValidateResponseFor()
    {
        $swagger = <<<JSON
{
    "swagger": "2.0",
    "info": {
        "title": "test",
        "version": "1.0"
    },
    "produces": [
        "application/json"
    ],
    "paths": {
        "/tests": {
            "get": {
                "responses": {
                    "200": {
                        "description": "Get the list of all the tests cases."
                    }
                }
            }
        }
    }
}
JSON;

        $prophecy = $this->prophesize(ResponseInterface::class);
        $prophecy->getStatusCode()->willReturn(200);
        $prophecy->getContentType()->willReturn('application/json');

        $validator = new SwaggerValidator($this->buildSwagger($swagger));
        $validator->validateResponseFor($prophecy->reveal(), PathItem::METHOD_GET, '/tests', 200);
    }

    /**
     * @test
     * @expectedException \WakeOnWeb\Component\Swagger\Test\Exception\StatusCodeException
     */
    public function testValidateResponseForThrowsAnExceptionWhenTheStatusCodeIsInvalid()
    {
        $swagger = <<<JSON
{
    "swagger": "2.0",
    "info": {
        "title": "test",
        "version": "1.0"
    },
    "produces": [
        "application/json"
    ],
    "paths": {
        "/tests": {
            "get": {
                "responses": {
                    "200": {
                        "description": "Get the list of all the tests cases."
                    }
                }
            }
        }
    }
}
JSON;

        $prophecy = $this->prophesize(ResponseInterface::class);
        $prophecy->getStatusCode()->willReturn(500);
        $prophecy->getContentType()->willReturn('application/json');

        $validator = new SwaggerValidator($this->buildSwagger($swagger));
        $validator->validateResponseFor($prophecy->reveal(), PathItem::METHOD_GET, '/tests', 200);
    }

    /**
     * @test
     * @expectedException \WakeOnWeb\Component\Swagger\Test\Exception\ContentTypeException
     */
    public function testValidateResponseForThrowsAnExceptionWhenTheContentTypeIsInvalid()
    {
        $swagger = <<<JSON
{
    "swagger": "2.0",
    "info": {
        "title": "test",
        "version": "1.0"
    },
    "produces": [
        "application/json"
    ],
    "paths": {
        "/tests": {
            "get": {
                "responses": {
                    "200": {
                        "description": "Get the list of all the tests cases."
                    }
                }
            }
        }
    }
}
JSON;

        $prophecy = $this->prophesize(ResponseInterface::class);
        $prophecy->getStatusCode()->willReturn(200);
        $prophecy->getContentType()->willReturn('application/xml');

        $validator = new SwaggerValidator($this->buildSwagger($swagger));
        $validator->validateResponseFor($prophecy->reveal(), PathItem::METHOD_GET, '/tests', 200);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function testValidateResponseForThrowsAnExceptionWhenTheMethodIsNotSupported()
    {
        $swagger = <<<JSON
{
    "swagger": "2.0",
    "info": {
        "title": "test",
        "version": "1.0"
    },
    "produces": [
        "application/json"
    ],
    "paths": {
        "/tests": {
            "get": {
                "responses": {
                    "200": {
                        "description": "Get the list of all the tests cases."
                    }
                }
            }
        }
    }
}
JSON;

        $validator = new SwaggerValidator($this->buildSwagger($swagger));
        $validator->validateResponseFor($this->prophesize(ResponseInterface::class)->reveal(), 'LINK', '/tests', 200);
    }

    /**
     * @param string $swagger
     *
     * @return Swagger
     */
    private function buildSwagger($swagger)
    {
        $factory = new SwaggerFactory();

        return $factory->build(json_decode($swagger, true));
    }
}
