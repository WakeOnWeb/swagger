<?php

namespace WakeOnWeb\Component\Swagger\Specification;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class Schema
{
    /**
     * @var array
     */
    private $jsonSchema;

    /**
     * @var Definitions
     */
    private $definitions;

    /**
     * @var string
     */
    private $discriminator;

    /**
     * @var bool
     */
    private $readOnly;

    /**
     * @var Xml|null
     */
    private $xml;

    /**
     * @var ExternalDocumentation|null
     */
    private $externalDocs;

    /**
     * @var mixed
     */
    private $example;

    /**
     * @param array                      $jsonSchema
     * @param Definitions                $definitions
     * @param string                     $discriminator
     * @param bool                       $readOnly
     * @param Xml|null                   $xml
     * @param ExternalDocumentation|null $externalDocs
     * @param mixed                      $example
     */
    public function __construct(array $jsonSchema, Definitions $definitions, $discriminator, $readOnly, Xml $xml = null, ExternalDocumentation $externalDocs = null, $example)
    {
        $this->jsonSchema = $jsonSchema;
        $this->definitions = $definitions;
        $this->discriminator = $discriminator;
        $this->readOnly = $readOnly;
        $this->xml = $xml;
        $this->externalDocs = $externalDocs;
        $this->example = $example;
    }

    /**
     * @return array
     */
    public function getJsonSchema()
    {
        return $this->jsonSchema;
    }

    /**
     * @return string
     */
    public function getJsonSchemaAsJson()
    {
        $definitions = [];

        foreach ($this->definitions->getDefinitions() as $name => $schema) {
            $definitions[$name] = $schema->getJsonSchema();
        }

        $schema = $this->jsonSchema + [
            'definitions' => $definitions
        ];

        return json_encode($schema);
    }

    /**
     * @return string
     */
    public function getDiscriminator()
    {
        return $this->discriminator;
    }

    /**
     * @return boolean
     */
    public function isReadOnly()
    {
        return $this->readOnly;
    }

    /**
     * @return Xml|null
     */
    public function getXml()
    {
        return $this->xml;
    }

    /**
     * @return ExternalDocumentation|null
     */
    public function getExternalDocs()
    {
        return $this->externalDocs;
    }

    /**
     * @return mixed
     */
    public function getExample()
    {
        return $this->example;
    }
}
