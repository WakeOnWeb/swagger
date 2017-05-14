<?php

namespace WakeOnWeb\Swagger;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Psr\Cache\CacheItemPoolInterface;
use WakeOnWeb\Swagger\Exception\ParseException;
use WakeOnWeb\Swagger\Loader\Exception\LoaderException;
use WakeOnWeb\Swagger\Loader\LoaderInterface;
use WakeOnWeb\Swagger\Specification\AbstractParameter;
use WakeOnWeb\Swagger\Specification\Contact;
use WakeOnWeb\Swagger\Specification\Definitions;
use WakeOnWeb\Swagger\Specification\Examples;
use WakeOnWeb\Swagger\Specification\ExternalDocumentation;
use WakeOnWeb\Swagger\Specification\Header;
use WakeOnWeb\Swagger\Specification\Headers;
use WakeOnWeb\Swagger\Specification\Info;
use WakeOnWeb\Swagger\Specification\Items;
use WakeOnWeb\Swagger\Specification\License;
use WakeOnWeb\Swagger\Specification\Operation;
use WakeOnWeb\Swagger\Specification\BodyParameter;
use WakeOnWeb\Swagger\Specification\Parameter;
use WakeOnWeb\Swagger\Specification\ParametersDefinitions;
use WakeOnWeb\Swagger\Specification\PathItem;
use WakeOnWeb\Swagger\Specification\Paths;
use WakeOnWeb\Swagger\Specification\Reference;
use WakeOnWeb\Swagger\Specification\Response;
use WakeOnWeb\Swagger\Specification\Responses;
use WakeOnWeb\Swagger\Specification\ResponsesDefinitions;
use WakeOnWeb\Swagger\Specification\Schema;
use WakeOnWeb\Swagger\Specification\Scopes;
use WakeOnWeb\Swagger\Specification\SecurityDefinitions;
use WakeOnWeb\Swagger\Specification\SecurityRequirement;
use WakeOnWeb\Swagger\Specification\SecurityScheme;
use WakeOnWeb\Swagger\Specification\Swagger;
use WakeOnWeb\Swagger\Specification\Tag;
use WakeOnWeb\Swagger\Specification\Xml;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class SwaggerFactory
{
    /**
     * @var LoaderInterface[]
     */
    private $loaders = [];

    /**
     * @var CacheItemPoolInterface|null
     */
    private $cacheItemPool;

    /**
     * @param CacheItemPoolInterface|null $cacheItemPool
     */
    public function __construct(CacheItemPoolInterface $cacheItemPool = null)
    {
        $this->cacheItemPool = $cacheItemPool ?: new ArrayCachePool();
    }

    /**
     * @param LoaderInterface $loader
     */
    public function addLoader(LoaderInterface $loader)
    {
        $this->loaders[] = $loader;
    }

    /**
     * @param string $filename
     *
     * @return Swagger
     *
     * @throws LoaderException
     * @throws ParseException
     */
    public function buildFrom($filename)
    {
        $item = $this->cacheItemPool->getItem($this->sanitizeKey($filename));

        if ($item->isHit()) {
            return unserialize($item->get());
        }

        foreach ($this->loaders as $loader) {
            if ($loader->supports($filename)) {
                $swagger = $this->build($loader->load($filename));

                $item->set(serialize($swagger));
                $this->cacheItemPool->save($item);

                return $swagger;
            }
        }

        throw LoaderException::fromFilename($filename);
    }

    /**
     * @param array $spec
     *
     * @return Swagger
     *
     * @throws ParseException
     */
    public function build(array $spec)
    {
        return $this->parseSwagger($spec);
    }

    /**
     * @param array $spec
     *
     * @return Swagger
     *
     * @throws ParseException
     */
    private function parseSwagger(array $spec)
    {
        $swagger = $this->getRequired($spec, 'swagger');

        if ($spec['swagger'] !== '2.0') {
            throw ParseException::fromVersionIncompatibility('2.0', $spec['swagger']);
        }

        $info = $this->parseInfo($this->getRequired($spec, 'info'));

        $host = $this->get($spec, 'host');

        $basePath = $this->get($spec, 'basePath');

        $schemes = $this->get($spec, 'schemes', []);

        $consumes = $this->get($spec, 'consumes', []);

        $produces = $this->get($spec, 'produces', []);

        $definitions = $this->parseDefinitions($this->get($spec, 'definitions', []));

        $parameters = $this->parseParametersDefinitions($this->get($spec, 'parameters', []));

        $responses = $this->parseResponsesDefinitions($this->get($spec, 'responses', []));

        $securityDefinitions = $this->parseSecurityDefinitions($this->get($spec, 'securityDefinitions', []));

        $security = [];

        foreach ($this->get($spec, 'security', []) as $subSpec) {
            $security[] = $this->parseSecurityRequirement($subSpec);
        }

        $tags = [];

        foreach ($this->get($spec, 'tags', []) as $subSpec) {
            $tags[] = $this->parseTag($subSpec);
        }

        $externalDocs = $this->get($spec, 'externalDocs');

        if ($externalDocs !== null) {
            $externalDocs = $this->parseExternalDocumentation($externalDocs);
        }

        $context = new Context($definitions, $parameters, $responses, $securityDefinitions);

        $paths = $this->parsePaths($this->getRequired($spec, 'paths'), $context);

        return new Swagger($swagger, $info, $host, $basePath, $schemes, $consumes, $produces, $paths, $definitions, $parameters, $responses, $securityDefinitions, $security, $tags, $externalDocs);
    }

    /**
     * @param array $spec
     *
     * @return Info
     *
     * @throws ParseException
     */
    private function parseInfo(array $spec)
    {
        $title = $this->getRequired($spec, 'title');

        $description = $this->get($spec, 'description');

        $termsOfService = $this->get($spec, 'termsOfService');

        $contact = $this->get($spec, 'contact');

        if ($contact !== null) {
            $contact = $this->parseContact($contact);
        }

        $license = $this->get($spec, 'license');

        if ($license !== null) {
            $license = $this->parseLicense($contact);
        }

        $version = $this->getRequired($spec, 'version');

        return new Info($title, $description, $termsOfService, $contact, $license, $version);
    }

    /**
     * @param array $spec
     *
     * @return Contact
     *
     * @throws ParseException
     */
    private function parseContact(array $spec)
    {
        $name = $this->get($spec, 'name');

        $url = $this->get($spec, 'url');

        $email = $this->get($spec, 'email');

        return new Contact($name, $url, $email);
    }

    /**
     * @param array $spec
     *
     * @return License
     *
     * @throws ParseException
     */
    private function parseLicense(array $spec)
    {
        $name = $this->get($spec, 'name');

        $url = $this->get($spec, 'url');

        return new License($name, $url);
    }

    /**
     * @param array   $spec
     * @param Context $context
     *
     * @return Paths
     *
     * @throws ParseException
     */
    private function parsePaths(array $spec, Context $context)
    {
        $paths = [];

        foreach ($spec as $path => $subSpec) {
            if (substr($path, 0, 1) !== '/') {
                throw ParseException::fromInvalidPathStart($path);
            }

            $paths[$path] = $this->parsePathItem($subSpec, $context);
        }

        return new Paths($paths);
    }

    /**
     * @param array   $spec
     * @param Context $context
     *
     * @return PathItem
     *
     * @throws ParseException
     */
    private function parsePathItem(array $spec, Context $context)
    {
        $ref = $this->get($spec, '$ref');

        if ($ref !== null) {
            // @todo: Load the external reference.
        }

        $get = $this->get($spec, 'get');

        if ($get !== null) {
            $get = $this->parseOperation($get, $context);
        }

        $put = $this->get($spec, 'put');

        if ($put !== null) {
            $put = $this->parseOperation($put, $context);
        }

        $post = $this->get($spec, 'post');

        if ($post !== null) {
            $post = $this->parseOperation($post, $context);
        }

        $delete = $this->get($spec, 'delete');

        if ($delete !== null) {
            $delete = $this->parseOperation($delete, $context);
        }

        $options = $this->get($spec, 'options');

        if ($options !== null) {
            $options = $this->parseOperation($options, $context);
        }

        $head = $this->get($spec, 'head');

        if ($head !== null) {
            $head = $this->parseOperation($head, $context);
        }

        $patch = $this->get($spec, 'patch');

        if ($patch !== null) {
            $patch = $this->parseOperation($patch, $context);
        }

        $parameters = [];

        foreach ($this->get($spec, 'parameters', []) as $parameter) {
            if ($this->isReference($parameter)) {
                $parameter = $this->parseReference($parameter);

                // @todo: Resolve parameter reference.
            } else {
                $parameter = $this->parseParameter($parameter);
            }

            $parameters[] = $parameter;
        }

        return new PathItem($get, $put, $post, $delete, $options, $head, $patch, $parameters);
    }

    /**
     * @param array $spec
     *
     * @return Reference
     *
     * @throws ParseException
     */
    private function parseReference(array $spec)
    {
        return new Reference($spec['$ref']);
    }

    /**
     * @param array $spec
     *
     * @return AbstractParameter
     *
     * @throws ParseException
     */
    private function parseParameter(array $spec)
    {
        $name = $this->getRequired($spec, 'name');

        $in = $this->getRequired($spec, 'in');

        $description = $this->get($spec, 'description');

        $required = $this->get($spec, 'required', false);

        if ($in === 'body') {
            $schema = $this->parseSchema($this->getRequired($spec, 'schema'));

            return new BodyParameter($name, $in, $description, $required, $schema);
        } else {
            $type = $this->getRequired($spec, 'type');

            $format = $this->get($spec, 'format');

            $allowEmptyValue = $this->get($spec, 'allowEmptyValue', false);

            if ($type === 'array') {
                $items = $this->parseItems($this->getRequired($spec, 'items'));
            } else {
                $items = null;
            }

            $collectionFormat = $this->get($spec, 'collectionFormat');

            $default = $this->get($spec, 'default');

            $maximum = $this->get($spec, 'maximum');

            $exclusiveMaximum = $this->get($spec, 'exclusiveMaximum');

            $minimum = $this->get($spec, 'minimum');

            $exclusiveMinimum = $this->get($spec, 'exclusiveMinimum');

            $maxLength = $this->get($spec, 'maxLength');

            $minLength = $this->get($spec, 'minLength');

            $pattern = $this->get($spec, 'pattern');

            $maxItems = $this->get($spec, 'maxItems');

            $minItems = $this->get($spec, 'minItems');

            $uniqueItems = $this->get($spec, 'uniqueItems');

            $enum = $this->get($spec, 'enum');

            $multipleOf = $this->get($spec, 'multipleOf');

            return new Parameter($name, $in, $description, $required, $type, $format, $allowEmptyValue, $items, $collectionFormat, $default, $maximum, $exclusiveMaximum, $minimum, $exclusiveMinimum, $maxLength, $minLength, $pattern, $maxItems, $minItems, $uniqueItems, $enum, $multipleOf);
        }
    }

    /**
     * @param array $spec
     *
     * @return Items
     *
     * @throws ParseException
     */
    private function parseItems(array $spec)
    {
        $type = $this->getRequired($spec, 'type');

        $format = $this->get($spec, 'format');

        if ($type === 'array') {
            $items = $this->parseItems($this->getRequired($spec, 'items'));
        } else {
            $items = null;
        }

        $collectionFormat = $this->get($spec, 'collectionFormat');

        $default = $this->get($spec, 'default');

        $maximum = $this->get($spec, 'maximum');

        $exclusiveMaximum = $this->get($spec, 'exclusiveMaximum');

        $minimum = $this->get($spec, 'minimum');

        $exclusiveMinimum = $this->get($spec, 'exclusiveMinimum');

        $maxLength = $this->get($spec, 'maxLength');

        $minLength = $this->get($spec, 'minLength');

        $pattern = $this->get($spec, 'pattern');

        $maxItems = $this->get($spec, 'maxItems');

        $minItems = $this->get($spec, 'minItems');

        $uniqueItems = $this->get($spec, 'uniqueItems');

        $enum = $this->get($spec, 'enum');

        $multipleOf = $this->get($spec, 'multipleOf');

        return new Items($type, $format, $items, $collectionFormat, $default, $maximum, $exclusiveMaximum, $minimum, $exclusiveMinimum, $maxLength, $minLength, $pattern, $maxItems, $minItems, $uniqueItems, $enum, $multipleOf);
    }

    /**
     * @param array   $spec
     * @param Context $context
     *
     * @return Operation
     *
     * @throws ParseException
     */
    private function parseOperation(array $spec, Context $context)
    {
        $tags = $this->get($spec, 'tags', []);

        $summary = $this->get($spec, 'summary');

        $description = $this->get($spec, 'description');

        $externalDocs = $this->get($spec, 'externalDocs');

        if ($externalDocs !== null) {
            $externalDocs = $this->parseExternalDocumentation($externalDocs);
        }

        $operationId = $this->get($spec, 'operationId');

        $consumes = $this->get($spec, 'consumes', []);

        $produces = $this->get($spec, 'produces', []);

        $parameters = [];

        foreach ($this->get($spec, 'parameters', []) as $parameter) {
            if ($this->isReference($parameter)) {
                $parameter = $this->parseReference($parameter);

                // @todo: Resolve "Parameter" reference.
            } else {
                $parameter = $this->parseParameter($parameter);
            }

            $parameters[] = $parameter;
        }

        $responses = $this->parseResponses($this->getRequired($spec, 'responses'), $context);

        $schemes = $this->get($spec, 'schemes', []);

        $deprecated = $this->get($spec, 'deprecated', false);

        $security = [];

        foreach ($this->get($spec, 'security', []) as $subSpec) {
            $security[] = $this->parseSecurityRequirement($subSpec);
        }

        return new Operation($tags, $summary, $description, $externalDocs, $operationId, $consumes, $produces, $parameters, $responses, $schemes, $deprecated, $security);
    }

    /**
     * @param array   $spec
     * @param Context $context
     *
     * @return Responses
     *
     * @throws ParseException
     */
    private function parseResponses(array $spec, Context $context)
    {
        $default = $this->get($spec, 'default');

        if ($default !== null) {
            if ($this->isReference($default)) {
                $default = $this->parseReference($default);

                // @todo: Resolve "Response" reference.
            } else {
                $default = $this->parseResponse($default, $context);
            }
        }

        $codes = array_filter(array_keys($spec), function ($key) {
            return preg_match('/[0-9]{3}/', $key) === 1;
        });

        $responses = [];

        foreach ($codes as $code) {
            $responses[$code] = $this->parseResponse($spec[$code], $context);
        }

        return new Responses($default, $responses);
    }

    /**
     * @param array   $spec
     * @param Context $context
     *
     * @return Response
     *
     * @throws ParseException
     */
    private function parseResponse(array $spec, Context $context)
    {
        $description = $this->getRequired($spec, 'description');

        $schema = $this->get($spec, 'schema');

        if ($schema !== null) {
            $schema = $this->parseSchema($schema, $context);
        }

        $headers = $this->get($spec, 'headers');

        if ($headers !== null) {
            $headers = $this->parseHeaders($headers);
        }

        $examples = $this->get($spec, 'examples');

        if ($examples !== null) {
            $examples = $this->parseExamples($examples);
        }

        return new Response($description, $schema, $headers, $examples);
    }

    /**
     * @param array        $spec
     * @param Context|null $context
     *
     * @return Schema
     *
     * @throws ParseException
     */
    private function parseSchema(array $spec, Context $context = null)
    {
        $discriminator = $this->get($spec, 'discriminator');

        $readOnly = $this->get($spec, 'readOnly');

        $xml = $this->get($spec, 'xml');

        if ($xml !== null) {
            $xml = $this->parseXml($xml);
        }

        $externalDocs = $this->get($spec, 'externalDocs');

        if ($externalDocs !== null) {
            $externalDocs = $this->parseExternalDocumentation($externalDocs);
        }

        $example = $this->get($spec, 'example');

        unset(
            $spec['discriminator'],
            $spec['readOnly'],
            $spec['xml'],
            $spec['externalDocs'],
            $spec['example']
        );

        $vendorSpecifics = array_filter(array_keys($spec), function ($key) {
            return preg_match('/^x-/', $key) === 1;
        });

        foreach ($vendorSpecifics as $vendorSpecific) {
            unset($spec[$vendorSpecific]);
        }

        $jsonSchema = $this->parseJsonSchema($spec);

        if ($context !== null) {
            $jsonSchema = $this->resolveJsonSchema($jsonSchema, $context);
        }

        return new Schema($jsonSchema, $discriminator, $readOnly, $xml, $externalDocs, $example);
    }

    /**
     * @param array $spec
     *
     * @return Xml
     *
     * @throws ParseException
     */
    private function parseXml(array $spec)
    {
        $name = $this->get($spec, 'name');

        $namespace = $this->get($spec, 'namespace');

        $prefix = $this->get($spec, 'prefix');

        $attribute = $this->get($spec, 'attribute');

        $wrapped = $this->get($spec, 'wrapped');

        return new Xml($name, $namespace, $prefix, $attribute, $wrapped);
    }

    /**
     * @param array $spec
     *
     * @return Headers
     *
     * @throws ParseException
     */
    private function parseHeaders(array $spec)
    {
        $headers = [];

        foreach ($spec as $name => $subSpec) {
            $headers[$name] = $this->parseHeader($subSpec);
        }

        return new Headers($headers);
    }

    /**
     * @param array $spec
     *
     * @return Header
     *
     * @throws ParseException
     */
    private function parseHeader(array $spec)
    {
        $description = $this->get($spec, 'description');

        $type = $this->getRequired($spec, 'type');

        $format = $this->get($spec, 'format');

        if ($type === 'array') {
            $items = $this->getRequired($spec, 'items');
        } else {
            $items = null;
        }

        $collectionFormat = $this->get($spec, 'collectionFormat');

        $default = $this->get($spec, 'default');

        $maximum = $this->get($spec, 'maximum');

        $exclusiveMaximum = $this->get($spec, 'exclusiveMaximum');

        $minimum = $this->get($spec, 'minimum');

        $exclusiveMinimum = $this->get($spec, 'exclusiveMinimum');

        $maxLength = $this->get($spec, 'maxLength');

        $minLength = $this->get($spec, 'minLength');

        $pattern = $this->get($spec, 'pattern');

        $maxItems = $this->get($spec, 'maxItems');

        $minItems = $this->get($spec, 'minItems');

        $uniqueItems = $this->get($spec, 'uniqueItems');

        $enum = $this->get($spec, 'enum');

        $multipleOf = $this->get($spec, 'multipleOf');

        return new Header($description, $type, $format, $items, $collectionFormat, $default, $maximum, $exclusiveMaximum, $minimum, $exclusiveMinimum, $maxLength, $minLength, $pattern, $maxItems, $minItems, $uniqueItems, $enum, $multipleOf);
    }

    /**
     * @param array $spec
     *
     * @return Examples
     *
     * @throws ParseException
     */
    private function parseExamples(array $spec)
    {
        return new Examples($spec);
    }

    /**
     * @param array $spec
     *
     * @return Definitions
     *
     * @throws ParseException
     */
    private function parseDefinitions(array $spec)
    {
        $definitions = [];

        foreach ($spec as $name => $subSpec) {
            $definitions[$name] = $this->parseSchema($subSpec);
        }

        return new Definitions($definitions);
    }

    /**
     * @param array $spec
     *
     * @return ParametersDefinitions
     *
     * @throws ParseException
     */
    private function parseParametersDefinitions(array $spec)
    {
        $parameters = [];

        foreach ($spec as $name => $subSpec) {
            $parameters[$name] = $this->parseParameter($subSpec);
        }

        return new ParametersDefinitions($parameters);
    }

    /**
     * @param array $spec
     *
     * @return ResponsesDefinitions
     *
     * @throws ParseException
     */
    private function parseResponsesDefinitions(array $spec)
    {
        $responses = [];

        foreach ($spec as $name => $subSpec) {
            $responses[$name] = $this->parseResponse($subSpec);
        }

        return new ResponsesDefinitions($responses);
    }

    /**
     * @param array $spec
     *
     * @return SecurityDefinitions
     *
     * @throws ParseException
     */
    private function parseSecurityDefinitions(array $spec)
    {
        $definitions = [];

        foreach ($spec as $name => $subSpec) {
            $definitions[$name] = $this->parseSecurityScheme($subSpec);
        }

        return new SecurityDefinitions($definitions);
    }

    /**
     * @param array $spec
     *
     * @return SecurityScheme
     *
     * @throws ParseException
     */
    private function parseSecurityScheme(array $spec)
    {
        $type = $this>$this->getRequired($spec, 'type');

        $description = $this->get($spec, 'description');

        $name = $this->getRequired($spec, 'name');

        $in = $this->getRequired($spec, 'in');

        $flow = $this->getRequired($spec, 'flow');

        $authorizationUrl = $this->getRequired($spec, 'authorizationUrl');

        $tokenUrl = $this->getRequired($spec, 'tokenUrl');

        $scopes = $this->parseScopes($this->getRequired($spec, 'scopes'));

        return new SecurityScheme($type, $description, $name, $in, $flow, $authorizationUrl, $tokenUrl, $scopes);
    }

    /**
     * @param array $spec
     *
     * @return Scopes
     *
     * @throws ParseException
     */
    private function parseScopes(array $spec)
    {
        return new Scopes($spec);
    }

    /**
     * @param array $spec
     *
     * @return SecurityRequirement
     *
     * @throws ParseException
     */
    private function parseSecurityRequirement(array $spec)
    {
        return new SecurityRequirement($spec);
    }

    /**
     * @param array $spec
     *
     * @return Tag
     *
     * @throws ParseException
     */
    private function parseTag(array $spec)
    {
        $name = $this->get($spec, 'name');

        $description = $this->get($spec, 'description');

        $externalDocs = $this->get($spec, 'externalDocs');

        if ($externalDocs !== null) {
            $externalDocs = $this->parseExternalDocumentation($spec);
        }

        return new Tag($name, $description, $externalDocs);
    }

    /**
     * @param array $spec
     *
     * @return ExternalDocumentation
     *
     * @throws ParseException
     */
    private function parseExternalDocumentation(array $spec)
    {
        $description = $this->get($spec, 'description');

        $url = $this->getRequired($spec, 'url');

        return new ExternalDocumentation($description, $url);
    }

    /**
     * @param array $spec
     *
     * @return array
     */
    private function parseJsonSchema(array $spec)
    {
        return $spec;
    }

    /**
     * @param array   $jsonSchema
     * @param Context $context
     *
     * @return array
     */
    private function resolveJsonSchema(array $jsonSchema, Context $context)
    {
        return [
            'definitions' => $context->getDefinitions(),
        ] + $jsonSchema;
    }

    /**
     * @param array $spec
     *
     * @return bool
     */
    private function isReference(array $spec)
    {
        return isset($spec['$ref']);
    }

    /**
     * @param array      $array
     * @param string     $key
     *
     * @return mixed
     *
     * @throws ParseException
     */
    private function getRequired(array $array, $key)
    {
        if (!isset($array[$key])) {
            throw ParseException::fromRequired($array, $key);
        }

        return $array[$key];
    }

    /**
     * @param array      $array
     * @param string     $key
     * @param mixed|null $default
     *
     * @return mixed
     */
    private function get(array $array, $key, $default = null)
    {
        return isset($array[$key]) ? $array[$key] : $default;
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    private function sanitizeKey($filename)
    {
        return md5(filemtime($filename));
    }
}
