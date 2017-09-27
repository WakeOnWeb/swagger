<?php

namespace WakeOnWeb\Component\Swagger\Specification;

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
     * @var ConsumesChain
     */
    private $consumes;

    /**
     * @var ProducesChain
     */
    private $produces;

    /**
     * @var ParametersChain
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
     * @param ConsumesChain              $consumes
     * @param ProducesChain              $produces
     * @param ParametersChain            $parameters
     * @param Responses                  $responses
     * @param string[]                   $schemes
     * @param bool                       $deprecated
     * @param SecurityRequirement[]      $security
     */
    public function __construct(array $tags, $summary, $description, ExternalDocumentation $externalDocs = null, $operationId, ConsumesChain $consumes, ProducesChain $produces, ParametersChain $parameters, Responses $responses, array $schemes, $deprecated, array $security)
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
     * @return ConsumesChain
     */
    public function getConsumes()
    {
        return $this->consumes;
    }

    /**
     * @return ProducesChain
     */
    public function getProduces()
    {
        return $this->produces;
    }

    /**
     * @return ParametersChain
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return BodyParameter|null
     */
    public function getBodyParameter()
    {
        foreach ($this->parameters->getParameters() as $parameter) {
            if ($parameter->getIn() === 'body') {
                return $parameter;
            }
        }
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
