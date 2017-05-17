# WakeOnWeb Swagger Validation Component [![Build Status](https://travis-ci.org/WakeOnWeb/swagger.svg?branch=master)](https://travis-ci.org/WakeOnWeb/swagger)

The WakeOnWeb Swagger Validation Component is an extensible component for validating API data using the 
[Swagger - OpenAPI](http://swagger.io) specification. The component supports both YAML and JSON Open API file formats. 
The component has very a small dependency set in order to be usable in different PHP frameworks.

The component uses:

- [PSR-6](http://www.php-fig.org/psr/psr-6/): For caching your OpenApi specification files
- [PSR-7](http://www.php-fig.org/psr/psr-7/): For processing HTTP messages (requests and responses)

Installation
------------

The component can easily be installaed using

    composer require wakeonweb/swagger
    
The component uses a JSON Schema validator, by default, the [justinrainbow/json-schema] (https://github.com/justinrainbow/json-schema) 
is in the dev dependencies. If you intend to use the component in production you need to execute:

    composer require justinrainbow/json-schema
    
Loading an OpenAPI specification file
-------------------------------------

The component supports both YAML and JSON OpenAPI format. Swagger files are loaded by the `SwaggerFactory`. The factory 
accepts a [PSR-6](http://www.php-fig.org/psr/psr-6/) `CacheItemPoolInterface`. If none provided it will use the
`ArrayCachePool` provided by [cache/array-adapter](https://github.com/php-cache/array-adapter).

```php
$factory = new SwaggerFactory();

// Register a YAML loader to load YAML Swagger files.
$factory->addLoader(new YamlLoader());

// Load the Swagger definition.
$swagger = $factory->buildFrom('/path/to/swagger/file.yml');
```

Executing this code will result in retrieving a tree representation of the specification into an instance of a `Swagger`
document. At the moment, the cache contains the instance of the `Swagger` document.

Creating the a content validator
--------------------------------

Content validation in the component is based on JSON Schema Validation. The OpenAPI Specification handles much more than this. 
For example it allows to define query string parameters or the format of any HTTP Headers. The component supports all kind 
of validation.

Content validators are used to validate the content of a request or a response. Any content validator must implement the 
`ContentValidatorInterface` and should be registered into an instance of a `ContentValidator`. The resulting instance can be 
used into an instance of a `SwaggerValidator`. 

```php
// Create a content validator that validates requests and responses bodies.
$contentValidator = new ContentValidator();

// Register a specific content validator that handle "application/json".
$contentValidator->registerContentValidator(new JustinRainbowJsonSchemaValidator());
```

Validating a response
---------------------

Validating response makes sense only for testing... As you are supposed to have valid code respectfull of your 
interface agreements in production! 

```php
// Create the validator and register the content validator.
$validator = new SwaggerValidator($swagger);
$validator->registerResponseValidator($contentValidator);

// Sample with Symfony Response....
$response = new Response(...);

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

Validating a request
--------------------

Validating response makes sense only for testing... As you are supposed to have valid code respectfull of your 
interface agreements in production! 

```php
// Create the validator and register the content validator.
$validator = new SwaggerValidator($swagger);
$validator->registerRequestValidator($contentValidator);

// Sample with Symfony Response....
$request = new Request(...);

$psr7Factory = new DiactorosFactory();

// Converts the response to a PRS-7 compliant format.
$response = $psr7Factory->createRequest($request);

try {
    // Validates the response against the required specification.
    $validator->validateRequestFor($response, PathItem::METHOD_GET, '/api/resource', 200);
} catch (SwaggerValidatorException $e) {
    // display $e message.
}
```
Complete sample
---------------

The following sample code demonstrates a complete usage of the component.

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
