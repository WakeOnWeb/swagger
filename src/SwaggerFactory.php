<?php

namespace UCS\Swagger;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Psr\Cache\CacheItemPoolInterface;
use UCS\Swagger\Exception\ParseException;
use UCS\Swagger\Loader\Exception\LoaderException;
use UCS\Swagger\Loader\LoaderInterface;
use UCS\Swagger\Specification\Definitions;
use UCS\Swagger\Specification\Examples;
use UCS\Swagger\Specification\ExternalDocumentation;
use UCS\Swagger\Specification\Header;
use UCS\Swagger\Specification\Headers;
use UCS\Swagger\Specification\Info;
use UCS\Swagger\Specification\Operation;
use UCS\Swagger\Specification\Parameter;
use UCS\Swagger\Specification\ParametersDefinitions;
use UCS\Swagger\Specification\PathItem;
use UCS\Swagger\Specification\Paths;
use UCS\Swagger\Specification\Reference;
use UCS\Swagger\Specification\Response;
use UCS\Swagger\Specification\Responses;
use UCS\Swagger\Specification\ResponsesDefinitions;
use UCS\Swagger\Specification\Schema;
use UCS\Swagger\Specification\SecurityDefinitions;
use UCS\Swagger\Specification\SecurityRequirement;
use UCS\Swagger\Specification\Swagger;
use UCS\Swagger\Specification\Tag;
use UCS\Swagger\Specification\Xml;

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

        // @todo: Validate "host".

        $basePath = $this->get($spec, 'basePath');

        // @todo: Validate "basePath".

        $schemes = $this->get($spec, 'schemes', []);

        $validSchemes = Swagger::getValidSchemes();

        foreach ($schemes as $scheme) {
            if (!in_array($scheme, $validSchemes)) {
                throw ParseException::fromInvalidScheme($validSchemes, $scheme);
            }
        }

        $consumes = $this->get($spec, 'consumes', []);

        // @todo: Validate "consumes".

        $produces = $this->get($spec, 'produces', []);

        // @todo: Validate "produces".

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
     * @todo: Parse the "Info" object.
     *
     * @param array $spec
     *
     * @return Info
     *
     * @throws ParseException
     */
    private function parseInfo(array $spec)
    {
        return new Info();
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
     * @todo: Parse the "Reference" object.
     *
     * @param array $spec
     *
     * @return Reference
     *
     * @throws ParseException
     */
    private function parseReference(array $spec)
    {
        return new Reference();
    }

    /**
     * @todo: Parse the "Parameter" object.
     *
     * @param array $spec
     *
     * @return Parameter
     *
     * @throws ParseException
     */
    private function parseParameter(array $spec)
    {
        return new Parameter();
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

        $validSchemes = Swagger::getValidSchemes();

        foreach ($schemes as $scheme) {
            if (!in_array($scheme, $validSchemes)) {
                throw ParseException::fromInvalidScheme($validSchemes, $scheme);
            }
        }

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
     * @todo: Parse the "Xml" object.
     *
     * @param array $spec
     *
     * @return Xml
     *
     * @throws ParseException
     */
    private function parseXml(array $spec)
    {
        return new Xml();
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
     * @todo: Parse the "Header" object.
     *
     * @param array $spec
     *
     * @return Header
     *
     * @throws ParseException
     */
    private function parseHeader(array $spec)
    {
        return new Header();
    }

    /**
     * @todo: Parse the "Examples" object.
     *
     * @param array $spec
     *
     * @return Examples
     *
     * @throws ParseException
     */
    private function parseExamples(array $spec)
    {
        return new Examples();
    }

    /**
     * @todo: Parse the "Definitions" object.
     *
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
     * @todo: Parse the "ParametersDefinitions" object.
     *
     * @param array $spec
     *
     * @return ParametersDefinitions
     *
     * @throws ParseException
     */
    private function parseParametersDefinitions(array $spec)
    {
        return new ParametersDefinitions();
    }

    /**
     * @todo: Parse the "ResponsesDefinitions" object.
     *
     * @param array $spec
     *
     * @return ResponsesDefinitions
     *
     * @throws ParseException
     */
    private function parseResponsesDefinitions(array $spec)
    {
        return new ResponsesDefinitions();
    }

    /**
     * @todo: Parse the "SecurityDefinitions" object.
     *
     * @param array $spec
     *
     * @return SecurityDefinitions
     *
     * @throws ParseException
     */
    private function parseSecurityDefinitions(array $spec)
    {
        return new SecurityDefinitions();
    }

    /**
     * @todo: Parse the "parseSecurityRequirement" object.
     *
     * @param array $spec
     *
     * @return SecurityRequirement
     *
     * @throws ParseException
     */
    private function parseSecurityRequirement(array $spec)
    {
        return new SecurityRequirement();
    }

    /**
     * @todo: Parse the "Tag" object.
     *
     * @param array $spec
     *
     * @return Tag
     *
     * @throws ParseException
     */
    private function parseTag(array $spec)
    {
        return new Tag();
    }

    /**
     * @todo: Parse the "ExternalDocumentation" object.
     *
     * @param array $spec
     *
     * @return ExternalDocumentation
     *
     * @throws ParseException
     */
    private function parseExternalDocumentation(array $spec)
    {
        return new ExternalDocumentation();
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
        return md5($filename);
    }
}