# WakeOnWeb Swagger Validation Component [![Build Status](https://travis-ci.org/WakeOnWeb/swagger.svg?branch=master)](https://travis-ci.org/WakeOnWeb/swagger)

Component for validating API data using the Swagger OpenAPI specification.
The component supports both YAML and JSON Swagger file formats.

Basic usage
-----------

```php
<?php

use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Component\HttpFoundation\Response;
use WakeOnWeb\Component\Swagger\Specification\PathItem;
use WakeOnWeb\Component\Swagger\SwaggerFactory;
use WakeOnWeb\Component\Swagger\Loader\YamlLoader;
use WakeOnWeb\Component\Swagger\Loader\JsonLoader;
use WakeOnWeb\Component\Swagger\Test\ContentValidator;
use WakeOnWeb\Component\Swagger\Test\Exception\SwaggerValidatorException;
use WakeOnWeb\Component\Swagger\Test\JustinRainbowJsonSchemaValidator;
use WakeOnWeb\Component\Swagger\Test\SwaggerValidator;

$factory = new SwaggerFactory();

// Register a YAML loader to load YAML Swagger files.
$factory->addLoader(new YamlLoader());

// Register a JSON loader to load JSON Swagger files.
$factory->addLoader(new JsonLoader());

// Load the Swagger definition.
$swagger = $factory->buildFrom('/path/to/swagger/file.yml');

// Create a content validator that validates requests and responses bodies.
$contentValidator = new ContentValidator();

// Register a specific content validator that handle "application/json".
$contentValidator->registerContentValidator(new JustinRainbowJsonSchemaValidator());

// Create the validator and register the content validator.
$validator = new SwaggerValidator($swagger);
$validator->registerResponseValidator($contentValidator);

$response = new Response(
    '{...}',
    200,
    [
        'Content-Type' => 'application/json',
    ]
);

$psr7Factory = new DiactorosFactory();

// Converts the response to a PRS-7 compliant format.
$response = $psr7Factory->createResponse($response);

try {
    // Validates the response against the required specification.
    $validator->validateResponseFor($response, PathItem::METHOD_GET, '/api/resource', 200);
} catch (SwaggerValidatorException $e) {
    // display $e message.
}
```
