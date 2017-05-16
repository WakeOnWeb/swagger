# WakeOnWeb Swagger Validation Component [![Build Status](https://travis-ci.org/WakeOnWeb/swagger.svg?branch=master)](https://travis-ci.org/WakeOnWeb/swagger)

Component for validating API data using the Swagger OpenAPI specification.
The component supports both YAML and JSON Swagger file formats.

Basic usage with YAML Swagger File
----------------------------------

```php
<?php

use Symfony\Component\HttpFoundation\Response;
use WakeOnWeb\Component\Swagger\Specification\PathItem;
use WakeOnWeb\Component\Swagger\SwaggerFactory;
use WakeOnWeb\Component\Swagger\Loader\YamlLoader;
use WakeOnWeb\Component\Swagger\Test\ContentValidator;
use WakeOnWeb\Component\Swagger\Test\Exception\SwaggerValidatorException;
use WakeOnWeb\Component\Swagger\Test\JustinRainbowJsonSchemaValidator;
use WakeOnWeb\Component\Swagger\Test\Response\SymfonyResponseAdapter;
use WakeOnWeb\Component\Swagger\Test\SwaggerValidator;

$factory = new SwaggerFactory();
$factory->addLoader(new YamlLoader());

$swagger = $factory->buildFrom('/path/to/swagger/file.yml');

$contentValidator = new ContentValidator();
$contentValidator->registerContentValidator(new JustinRainbowJsonSchemaValidator());

$validator = new SwaggerValidator($swagger);
$validator->registerResponseValidator($contentValidator);

$response = new Response(
    '{...}',
    200,
    [
        'Content-Type' => 'application/json',
    ]
);
$response = new SymfonyResponseAdapter($response);

try {
    $validator->validateResponseFor($response, PathItem::METHOD_GET, '/api/resource', 200);
} catch (SwaggerValidatorException $e) {
    // display $e message.
}
```

Basic usage with JSON Swagger File
----------------------------------

```php
<?php

use Symfony\Component\HttpFoundation\Response;
use WakeOnWeb\Component\Swagger\Specification\PathItem;
use WakeOnWeb\Component\Swagger\SwaggerFactory;
use WakeOnWeb\Component\Swagger\Loader\JsonLoader;
use WakeOnWeb\Component\Swagger\Test\ContentValidator;
use WakeOnWeb\Component\Swagger\Test\Exception\SwaggerValidatorException;
use WakeOnWeb\Component\Swagger\Test\JustinRainbowJsonSchemaValidator;
use WakeOnWeb\Component\Swagger\Test\Response\SymfonyResponseAdapter;
use WakeOnWeb\Component\Swagger\Test\SwaggerValidator;

$factory = new SwaggerFactory();
$factory->addLoader(new JsonLoader());

$swagger = $factory->buildFrom('/path/to/swagger/file.json');

$contentValidator = new ContentValidator();
$contentValidator->registerContentValidator(new JustinRainbowJsonSchemaValidator());

$validator = new SwaggerValidator($swagger);
$validator->registerResponseValidator($contentValidator);

$response = new Response(
    '{...}',
    200,
    [
        'Content-Type' => 'application/json',
    ]
);
$response = new SymfonyResponseAdapter($response);

try {
    $validator->validateResponseFor($response, PathItem::METHOD_GET, '/api/resource', 200);
} catch (SwaggerValidatorException $e) {
    // display $e message.
}
```
