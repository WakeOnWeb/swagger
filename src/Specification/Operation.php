<?php

namespace WakeOnWeb\Swagger\Specification;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class Operation
{
    /**
     * @var string[]
     */
    private $tags;

    /**
     * @var string
     */
    private $summary;

    /**
     * @var string
     */
    private $description;

    /**
     * @var ExternalDocumentation|null
     */
    private $externalDocs;

    /**
     * @var string
     */
    private $operationId;

    /**
     * @var string[]
     */
    private $consumes;

    /**
     * @var string[]
     */
    private $produces;

    /**
     * @var Parameter[]|Reference[]
     */
    private $parameters;

    /**
     * @var Responses
     */
    private $responses;

    /**
     * @var string[]
     */
    private $schemes;

    /**
     * @var bool
     */
    private $deprecated;

    /**
     * @var SecurityRequirement[]
     */
    private $security;

    /**
     * @param string[]                   $tags
     * @param string                     $summary
     * @param string                     $description
     * @param ExternalDocumentation|null $externalDocs
     * @param string                     $operationId
     * @param string[]                   $consumes
     * @param string[]                   $produces
     * @param Parameter[]|Reference[]    $parameters
     * @param Responses                  $responses
     * @param string[]                   $schemes
     * @param bool                       $deprecated
     * @param SecurityRequirement[]      $security
     */
    public function __construct(array $tags, $summary, $description, ExternalDocumentation $externalDocs = null, $operationId, array $consumes, array $produces, array $parameters, Responses $responses, array $schemes, $deprecated, array $security)
    {
        $this->tags = $tags;
        $this->summary = $summary;
        $this->description = $description;
        $this->externalDocs = $externalDocs;
        $this->operationId = $operationId;
        $this->consumes = $consumes;
        $this->produces = $produces;
        $this->parameters = $parameters;
        $this->responses = $responses;
        $this->schemes = $schemes;
        $this->deprecated = $deprecated;
        $this->security = $security;
    }

    /**
     * @return string[]
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return ExternalDocumentation|null
     */
    public function getExternalDocs()
    {
        return $this->externalDocs;
    }

    /**
     * @return string
     */
    public function getOperationId()
    {
        return $this->operationId;
    }

    /**
     * @return string[]
     */
    public function getConsumes()
    {
        return $this->consumes;
    }

    /**
     * @return string[]
     */
    public function getProduces()
    {
        return $this->produces;
    }

    /**
     * @return Parameter[]|Reference[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return Responses
     */
    public function getResponses()
    {
        return $this->responses;
    }

    /**
     * @return string[]
     */
    public function getSchemes()
    {
        return $this->schemes;
    }

    /**
     * @return boolean
     */
    public function isDeprecated()
    {
        return $this->deprecated;
    }

    /**
     * @return SecurityRequirement[]
     */
    public function getSecurity()
    {
        return $this->security;
    }
}