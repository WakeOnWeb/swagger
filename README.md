# Swagger

Component for validating API data using the Swagger OpenAPI specification.
The component supports both YAML and JSON Swagger file formats.

Basic usage with YAML Swagger File
----------------------------------

```php
<?php

use Symfony\Component\HttpFoundation\Response;
use UCS\Swagger\Specification\PathItem;
use UCS\Swagger\SwaggerFactory;
use UCS\Swagger\Loader\YamlLoader;
use UCS\Swagger\Test\Exception\SwaggerValidatorException;
use UCS\Swagger\Test\JValJsonSchemaValidator;
use UCS\Swagger\Test\Response\SymfonyResponseAdapter;
use UCS\Swagger\Test\SwaggerValidator;

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
use UCS\Swagger\Specification\PathItem;
use UCS\Swagger\SwaggerFactory;
use UCS\Swagger\Loader\JsonLoader;
use UCS\Swagger\Test\Exception\SwaggerValidatorException;
use UCS\Swagger\Test\JValJsonSchemaValidator;
use UCS\Swagger\Test\Response\SymfonyResponseAdapter;
use UCS\Swagger\Test\SwaggerValidator;

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