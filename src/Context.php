<?php

namespace UCS\Swagger;

use UCS\Swagger\Specification\Definitions;
use UCS\Swagger\Specification\ParametersDefinitions;
use UCS\Swagger\Specification\ResponsesDefinitions;
use UCS\Swagger\Specification\SecurityDefinitions;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class Context
{
    /**
     * @var Definitions
     */
    private $definitions;

    /**
     * @var ParametersDefinitions
     */
    private $parameters;

    /**
     * @var ResponsesDefinitions
     */
    private $responses;

    /**
     * @var SecurityDefinitions
     */
    private $securityDefinitions;

    /**
     * @param Definitions           $definitions
     * @param ParametersDefinitions $parameters
     * @param ResponsesDefinitions  $responses
     * @param SecurityDefinitions   $securityDefinitions
     */
    public function __construct(Definitions $definitions, ParametersDefinitions $parameters, ResponsesDefinitions $responses, SecurityDefinitions $securityDefinitions)
    {
        $this->definitions = $definitions;
        $this->parameters = $parameters;
        $this->responses = $responses;
        $this->securityDefinitions = $securityDefinitions;
    }

    /**
     * @return Definitions
     */
    public function getDefinitions()
    {
        return $this->definitions;
    }

    /**
     * @return ParametersDefinitions
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return ResponsesDefinitions
     */
    public function getResponses()
    {
        return $this->responses;
    }

    /**
     * @return SecurityDefinitions
     */
    public function getSecurityDefinitions()
    {
        return $this->securityDefinitions;
    }
}