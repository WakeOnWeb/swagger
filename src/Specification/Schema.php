<?php

namespace WakeOnWeb\Swagger\Specification;

use JsonSerializable;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class Schema implements JsonSerializable
{
    /**
     * @var array
     */
    private $jsonSchema;

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
     * @param string                     $discriminator
     * @param bool                       $readOnly
     * @param Xml|null                   $xml
     * @param ExternalDocumentation|null $externalDocs
     * @param mixed                      $example
     */
    public function __construct(array $jsonSchema, $discriminator, $readOnly, Xml $xml = null, ExternalDocumentation $externalDocs = null, $example)
    {
        $this->jsonSchema = $jsonSchema;
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
        return json_encode($this);
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

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->jsonSchema;
    }
}