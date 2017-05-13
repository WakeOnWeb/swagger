# WakeOnWeb Swagger Validation Component [![Build Status](https://travis-ci.org/WakeOnWeb/swagger.svg?branch=master)](https://travis-ci.org/WakeOnWeb/swagger)

Component for validating API data using the Swagger OpenAPI specification.
The component supports both YAML and JSON Swagger file formats.

Basic usage with YAML Swagger File
----------------------------------

```php
<?php

use Symfony\Component\HttpFoundation\Response;
use WakeOnWeb\Swagger\Specification\PathItem;
use WakeOnWeb\Swagger\SwaggerFactory;
use WakeOnWeb\Swagger\Loader\YamlLoader;
use WakeOnWeb\Swagger\Test\Exception\SwaggerValidatorException;
use WakeOnWeb\Swagger\Test\JValJsonSchemaValidator;
use WakeOnWeb\Swagger\Test\Response\SymfonyResponseAdapter;
use WakeOnWeb\Swagger\Test\SwaggerValidator;

$factory = new SwaggerFactory();
$factory->addLoader(new YamlLoader());

$swagger = $factory->buildFrom('/path/to/swagger/file.yml');

$validator = new SwaggerValidator($swagger);
$validator->registerContentValidator(new JValJsonSchemaValidator());

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
use WakeOnWeb\Swagger\Specification\PathItem;
use WakeOnWeb\Swagger\SwaggerFactory;
use WakeOnWeb\Swagger\Loader\JsonLoader;
use WakeOnWeb\Swagger\Test\Exception\SwaggerValidatorException;
use WakeOnWeb\Swagger\Test\JValJsonSchemaValidator;
use WakeOnWeb\Swagger\Test\Response\SymfonyResponseAdapter;
use WakeOnWeb\Swagger\Test\SwaggerValidator;

$factory = new SwaggerFactory();
$factory->addLoader(new JsonLoader());

$swagger = $factory->buildFrom('/path/to/swagger/file.json');

$validator = new SwaggerValidator($swagger);
$validator->registerContentValidator(new JValJsonSchemaValidator());

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