<?php

namespace WakeOnWeb\Component\Swagger;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Psr\Cache\CacheItemPoolInterface;
use WakeOnWeb\Component\Swagger\Exception\ParseException;
use WakeOnWeb\Component\Swagger\Loader\Exception\LoaderException;
use WakeOnWeb\Component\Swagger\Loader\LoaderInterface;
use WakeOnWeb\Component\Swagger\Specification\AbstractParameter;
use WakeOnWeb\Component\Swagger\Specification\ConsumesChain;
use WakeOnWeb\Component\Swagger\Specification\Contact;
use WakeOnWeb\Component\Swagger\Specification\Definitions;
use WakeOnWeb\Component\Swagger\Specification\Examples;
use WakeOnWeb\Component\Swagger\Specification\ExternalDocumentation;
use WakeOnWeb\Component\Swagger\Specification\Header;
use WakeOnWeb\Component\Swagger\Specification\Headers;
use WakeOnWeb\Component\Swagger\Specification\Info;
use WakeOnWeb\Component\Swagger\Specification\Items;
use WakeOnWeb\Component\Swagger\Specification\License;
use WakeOnWeb\Component\Swagger\Specification\Operation;
use WakeOnWeb\Component\Swagger\Specification\BodyParameter;
use WakeOnWeb\Component\Swagger\Specification\Parameter;
use WakeOnWeb\Component\Swagger\Specification\ParameterReference;
use WakeOnWeb\Component\Swagger\Specification\ParametersChain;
use WakeOnWeb\Component\Swagger\Specification\ParametersDefinitions;
use WakeOnWeb\Component\Swagger\Specification\PathItem;
use WakeOnWeb\Component\Swagger\Specification\Paths;
use WakeOnWeb\Component\Swagger\Specification\ProducesChain;
use WakeOnWeb\Component\Swagger\Specification\Response;
use WakeOnWeb\Component\Swagger\Specification\ResponseReference;
use WakeOnWeb\Component\Swagger\Specification\Responses;
use WakeOnWeb\Component\Swagger\Specification\ResponsesDefinitions;
use WakeOnWeb\Component\Swagger\Specification\Schema;
use WakeOnWeb\Component\Swagger\Specification\Scopes;
use WakeOnWeb\Component\Swagger\Specification\SecurityDefinitions;
use WakeOnWeb\Component\Swagger\Specification\SecurityRequirement;
use WakeOnWeb\Component\Swagger\Specification\SecurityScheme;
use WakeOnWeb\Component\Swagger\Specification\Swagger;
use WakeOnWeb\Component\Swagger\Specification\Tag;
use WakeOnWeb\Component\Swagger\Specification\Xml;

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

        $chains = [
            'produces' => $produces = new ProducesChain(),
            'consumes' => $consumes = new ConsumesChain(),
            'definitions' => $definitions = new Definitions(),
            'responses_definitions' => $responses = new ResponsesDefinitions(),
            'parameters_definition' => $parameters = new ParametersDefinitions(),
        ];

        $info = $this->parseInfo($this->getRequired($spec, 'info'), $chains);

        $host = $this->get($spec, 'host');

        $basePath = $this->get($spec, 'basePath');

        $schemes = $this->get($spec, 'schemes', []);

        $consumes->setConsumes($this->get($spec, 'consumes', []));

        $produces->setProduces($this->get($spec, 'produces', []));

        $definitions->setDefinitions($this->parseDefinitions($this->get($spec, 'definitions', []), $chains));

        $parameters->setDefinitions($this->parseParametersDefinitions($this->get($spec, 'parameters', []), $chains));

        $responses->setDefinitions($this->parseResponsesDefinitions($this->get($spec, 'responses', []), $chains));

        $securityDefinitions = $this->parseSecurityDefinitions($this->get($spec, 'securityDefinitions', []), $chains);

        $security = [];

        foreach ($this->get($spec, 'security', []) as $subSpec) {
            $security[] = $this->parseSecurityRequirement($subSpec, $chains);
        }

        $tags = [];

        foreach ($this->get($spec, 'tags', []) as $subSpec) {
            $tags[] = $this->parseTag($subSpec, $chains);
        }

        $externalDocs = $this->get($spec, 'externalDocs');

        if ($externalDocs !== null) {
            $externalDocs = $this->parseExternalDocumentation($externalDocs, $chains);
        }

        $paths = $this->parsePaths($this->getRequired($spec, 'paths'), $chains);

        return new Swagger($swagger, $info, $host, $basePath, $schemes, $consumes, $produces, $paths, $definitions, $parameters, $responses, $securityDefinitions, $security, $tags, $externalDocs);
    }

    /**
     * @param array $spec
     * @param array $chains
     *
     * @return Info
     *
     * @throws ParseException
     */
    private function parseInfo(array $spec, array $chains)
    {
        $title = $this->getRequired($spec, 'title');

        $description = $this->get($spec, 'description');

        $termsOfService = $this->get($spec, 'termsOfService');

        $contact = $this->get($spec, 'contact');

        if ($contact !== null) {
            $contact = $this->parseContact($contact, $chains);
        }

        $license = $this->get($spec, 'license');

        if ($license !== null) {
            $license = $this->parseLicense($license, $chains);
        }

        $version = $this->getRequired($spec, 'version');

        return new Info($title, $description, $termsOfService, $contact, $license, $version);
    }

    /**
     * @param array $spec
     * @param array $chains
     *
     * @return Contact
     *
     * @throws ParseException
     */
    private function parseContact(array $spec, array $chains)
    {
        $name = $this->get($spec, 'name');

        $url = $this->get($spec, 'url');

        $email = $this->get($spec, 'email');

        return new Contact($name, $url, $email);
    }

    /**
     * @param array $spec
     * @param array $chains
     *
     * @return License
     *
     * @throws ParseException
     */
    private function parseLicense(array $spec, array $chains)
    {
        $name = $this->get($spec, 'name');

        $url = $this->get($spec, 'url');

        return new License($name, $url);
    }

    /**
     * @param array $spec
     * @param array $chains
     *
     * @return Paths
     *
     * @throws ParseException
     */
    private function parsePaths(array $spec, array $chains)
    {
        $paths = [];

        foreach ($spec as $path => $subSpec) {
            if (substr($path, 0, 1) !== '/') {
                throw ParseException::fromInvalidPathStart($path);
            }

            $paths[$path] = $this->parsePathItem($subSpec, $chains);
        }

        return new Paths($paths);
    }

    /**
     * @param array $spec
     * @param array $chains
     *
     * @return PathItem
     *
     * @throws ParseException
     */
    private function parsePathItem(array $spec, array $chains)
    {
        $ref = $this->get($spec, '$ref');

        if ($ref !== null) {
            // @todo: Load the external reference.
        }

        $params = [];

        foreach ($this->get($spec, 'parameters', []) as $parameter) {
            if ($this->isReference($parameter)) {
                $parameter = new ParameterReference($parameter['$ref'], $chains['parameters_definition']);
            } else {
                $parameter = $this->parseParameter($parameter, $chains);
            }

            $params[] = $parameter;
        }

        $chains['parameters'] = $parameters = new ParametersChain();
        $parameters->setParameters($params);

        $get = $this->get($spec, 'get');

        if ($get !== null) {
            $get = $this->parseOperation($get, $chains);
        }

        $put = $this->get($spec, 'put');

        if ($put !== null) {
            $put = $this->parseOperation($put, $chains);
        }

        $post = $this->get($spec, 'post');

        if ($post !== null) {
            $post = $this->parseOperation($post, $chains);
        }

        $delete = $this->get($spec, 'delete');

        if ($delete !== null) {
            $delete = $this->parseOperation($delete, $chains);
        }

        $options = $this->get($spec, 'options');

        if ($options !== null) {
            $options = $this->parseOperation($options, $chains);
        }

        $head = $this->get($spec, 'head');

        if ($head !== null) {
            $head = $this->parseOperation($head, $chains);
        }

        $patch = $this->get($spec, 'patch');

        if ($patch !== null) {
            $patch = $this->parseOperation($patch, $chains);
        }

        return new PathItem($get, $put, $post, $delete, $options, $head, $patch, $parameters);
    }

    /**
     * @param array $spec
     * @param array $chains
     *
     * @return AbstractParameter
     *
     * @throws ParseException
     */
    private function parseParameter(array $spec, array $chains)
    {
        $name = $this->getRequired($spec, 'name');

        $in = $this->getRequired($spec, 'in');

        $description = $this->get($spec, 'description');

        $required = $this->get($spec, 'required', false);

        if ($in === 'body') {
            $schema = $this->parseSchema($this->getRequired($spec, 'schema'), $chains);

            return new BodyParameter($name, $in, $description, $required, $schema);
        } else {
            $type = $this->getRequired($spec, 'type');

            $format = $this->get($spec, 'format');

            $allowEmptyValue = $this->get($spec, 'allowEmptyValue', false);

            if ($type === 'array') {
                $items = $this->parseItems($this->getRequired($spec, 'items'), $chains);
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
     * @param array $chains
     *
     * @return Items
     *
     * @throws ParseException
     */
    private function parseItems(array $spec, array $chains)
    {
        $type = $this->getRequired($spec, 'type');

        $format = $this->get($spec, 'format');

        if ($type === 'array') {
            $items = $this->parseItems($this->getRequired($spec, 'items'), $chains);
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
     * @param array $spec
     * @param array $chains
     *
     * @return Operation
     *
     * @throws ParseException
     */
    private function parseOperation(array $spec, array $chains)
    {
        $chains['produces'] = $produces = new ProducesChain($chains['produces']);
        $chains['consumes'] = $consumes = new ConsumesChain($chains['consumes']);

        $tags = $this->get($spec, 'tags', []);

        $summary = $this->get($spec, 'summary');

        $description = $this->get($spec, 'description');

        $externalDocs = $this->get($spec, 'externalDocs');

        if ($externalDocs !== null) {
            $externalDocs = $this->parseExternalDocumentation($externalDocs, $chains);
        }

        $operationId = $this->get($spec, 'operationId');

        $consumes->setConsumes($this->get($spec, 'consumes', []));

        $produces->setProduces($this->get($spec, 'produces', []));

        $params = [];

        foreach ($this->get($spec, 'parameters', []) as $parameter) {
            if ($this->isReference($parameter)) {
                $parameter = new ParameterReference($parameter['$ref'], $chains['parameters_definition']);
            } else {
                $parameter = $this->parseParameter($parameter, $chains);
            }

            $params[] = $parameter;
        }

        $parameters = new ParametersChain($chains['parameters']);
        $parameters->setParameters($params);

        $responses = $this->parseResponses($this->getRequired($spec, 'responses'), $chains);

        $schemes = $this->get($spec, 'schemes', []);

        $deprecated = $this->get($spec, 'deprecated', false);

        $security = [];

        foreach ($this->get($spec, 'security', []) as $subSpec) {
            $security[] = $this->parseSecurityRequirement($subSpec, $chains);
        }

        return new Operation($tags, $summary, $description, $externalDocs, $operationId, $consumes, $produces, $parameters, $responses, $schemes, $deprecated, $security);
    }

    /**
     * @param array $spec
     * @param array $chains
     *
     * @return Responses
     *
     * @throws ParseException
     */
    private function parseResponses(array $spec, array $chains)
    {
        $default = $this->get($spec, 'default');

        if ($default !== null) {
            if ($this->isReference($default)) {
                $defaultResponseReference = new ResponseReference($default['$ref'], $chains['responses_definitions']);
                $default = $defaultResponseReference->resolve();
            } else {
                $default = $this->parseResponse($default, $chains);
            }
        }

        $codes = array_filter(array_keys($spec), function ($key) {
            return preg_match('/[0-9]{3}/', $key) === 1;
        });

        $responses = [];

        foreach ($codes as $code) {
            $subSpec = $spec[$code];

            if ($this->isReference($subSpec)) {
                $responses[$code] = new ResponseReference($subSpec['$ref'], $chains['responses_definitions']);
            } else {
                $responses[$code] = $this->parseResponse($subSpec, $chains);
            }
        }

        return new Responses($default, $responses);
    }

    /**
     * @param array $spec
     * @param array $chains
     *
     * @return Response
     *
     * @throws ParseException
     */
    private function parseResponse(array $spec, array $chains)
    {
        $description = $this->getRequired($spec, 'description');

        $schema = $this->get($spec, 'schema');

        if ($schema !== null) {
            $schema = $this->parseSchema($schema, $chains);
        }

        $headers = $this->get($spec, 'headers');

        if ($headers !== null) {
            $headers = $this->parseHeaders($headers, $chains);
        }

        $examples = $this->get($spec, 'examples');

        if ($examples !== null) {
            $examples = $this->parseExamples($examples, $chains);
        }

        return new Response($description, $schema, $headers, $examples);
    }

    /**
     * @param array $spec
     * @param array $chains
     *
     * @return Schema
     *
     * @throws ParseException
     */
    private function parseSchema(array $spec, array $chains)
    {
        $discriminator = $this->get($spec, 'discriminator');

        $readOnly = $this->get($spec, 'readOnly');

        $xml = $this->get($spec, 'xml');

        if ($xml !== null) {
            $xml = $this->parseXml($xml, $chains);
        }

        $externalDocs = $this->get($spec, 'externalDocs');

        if ($externalDocs !== null) {
            $externalDocs = $this->parseExternalDocumentation($externalDocs, $chains);
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

        $jsonSchema = $this->parseJsonSchema($spec, $chains);

        return new Schema($jsonSchema, $chains['definitions'], $discriminator, $readOnly, $xml, $externalDocs, $example);
    }

    /**
     * @param array $spec
     * @param array $chains
     *
     * @return Xml
     *
     * @throws ParseException
     */
    private function parseXml(array $spec, array $chains)
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
     * @param array $chains
     *
     * @return Headers
     *
     * @throws ParseException
     */
    private function parseHeaders(array $spec, array $chains)
    {
        $headers = [];

        foreach ($spec as $name => $subSpec) {
            $headers[$name] = $this->parseHeader($subSpec, $chains);
        }

        return new Headers($headers);
    }

    /**
     * @param array $spec
     * @param array $chains
     *
     * @return Header
     *
     * @throws ParseException
     */
    private function parseHeader(array $spec, array $chains)
    {
        $description = $this->get($spec, 'description');

        $type = $this->getRequired($spec, 'type');

        $format = $this->get($spec, 'format');

        if ($type === 'array') {
            $items = $this->parseItems($this->getRequired($spec, 'items'), $chains);
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
     * @param array $chains
     *
     * @return Examples
     *
     * @throws ParseException
     */
    private function parseExamples(array $spec, array $chains)
    {
        return new Examples($spec);
    }

    /**
     * @param array $spec
     * @param array $chains
     *
     * @return Schema[]
     *
     * @throws ParseException
     */
    private function parseDefinitions(array $spec, array $chains)
    {
        $definitions = [];

        foreach ($spec as $name => $subSpec) {
            $definitions[$name] = $this->parseSchema($subSpec, $chains);
        }

        return $definitions;
    }

    /**
     * @param array $spec
     * @param array $chains
     *
     * @return AbstractParameter[]
     *
     * @throws ParseException
     */
    private function parseParametersDefinitions(array $spec, array $chains)
    {
        $parameters = [];

        foreach ($spec as $name => $subSpec) {
            $parameters[$name] = $this->parseParameter($subSpec, $chains);
        }

        return $parameters;
    }

    /**
     * @param array $spec
     * @param array $chains
     *
     * @return Response[]
     *
     * @throws ParseException
     */
    private function parseResponsesDefinitions(array $spec, array $chains)
    {
        $responses = [];

        foreach ($spec as $name => $subSpec) {
            $responses[$name] = $this->parseResponse($subSpec, $chains);
        }

        return $responses;
    }

    /**
     * @param array $spec
     * @param array $chains
     *
     * @return SecurityDefinitions
     *
     * @throws ParseException
     */
    private function parseSecurityDefinitions(array $spec, array $chains)
    {
        $definitions = [];

        foreach ($spec as $name => $subSpec) {
            $definitions[$name] = $this->parseSecurityScheme($subSpec, $chains);
        }

        return new SecurityDefinitions($definitions);
    }

    /**
     * @param array $spec
     * @param array $chains
     *
     * @return SecurityScheme
     *
     * @throws ParseException
     */
    private function parseSecurityScheme(array $spec, array $chains)
    {
        $type = $this>$this->getRequired($spec, 'type');

        $description = $this->get($spec, 'description');

        if ($type === 'apiKey') {
            $name = $this->getRequired($spec, 'name');

            $in = $this->getRequired($spec, 'in');
        } else {
            $name = $in = null;
        }

        if ($type === 'oauth2') {
            $flow = $this->getRequired($spec, 'flow');

            $authorizationUrl = $this->getRequired($spec, 'authorizationUrl');

            $tokenUrl = $this->getRequired($spec, 'tokenUrl');

            $scopes = $this->parseScopes($this->getRequired($spec, 'scopes'), $chains);
        } else {
            $flow = $authorizationUrl = $tokenUrl = $scopes = null;
        }

        return new SecurityScheme($type, $description, $name, $in, $flow, $authorizationUrl, $tokenUrl, $scopes);
    }

    /**
     * @param array $spec
     * @param array $chains
     *
     * @return Scopes
     *
     * @throws ParseException
     */
    private function parseScopes(array $spec, array $chains)
    {
        return new Scopes($spec);
    }

    /**
     * @param array $spec
     * @param array $chains
     *
     * @return SecurityRequirement
     *
     * @throws ParseException
     */
    private function parseSecurityRequirement(array $spec, array $chains)
    {
        return new SecurityRequirement($spec);
    }

    /**
     * @param array $spec
     * @param array $chains
     *
     * @return Tag
     *
     * @throws ParseException
     */
    private function parseTag(array $spec, array $chains)
    {
        $name = $this->get($spec, 'name');

        $description = $this->get($spec, 'description');

        $externalDocs = $this->get($spec, 'externalDocs');

        if ($externalDocs !== null) {
            $externalDocs = $this->parseExternalDocumentation($externalDocs, $chains);
        }

        return new Tag($name, $description, $externalDocs);
    }

    /**
     * @param array $spec
     * @param array $chains
     *
     * @return ExternalDocumentation
     *
     * @throws ParseException
     */
    private function parseExternalDocumentation(array $spec, array $chains)
    {
        $description = $this->get($spec, 'description');

        $url = $this->getRequired($spec, 'url');

        return new ExternalDocumentation($description, $url);
    }

    /**
     * @param array $spec
     * @param array $chains
     *
     * @return array
     */
    private function parseJsonSchema(array $spec, array $chains)
    {
        return $spec;
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
