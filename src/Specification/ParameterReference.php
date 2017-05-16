<?php

namespace WakeOnWeb\Component\Swagger\Specification;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class ParameterReference
{
    /**
     * @var string
     */
    private $ref;

    /**
     * @var ParametersDefinitions
     */
    private $parameters;

    /**
     * @param string               $ref
     * @param ParametersDefinitions $parameters
     */
    public function __construct($ref, ParametersDefinitions $parameters)
    {
        $this->ref = $ref;
        $this->parameters = $parameters;
    }

    /**
     * @return Parameter
     */
    public function resolve()
    {
        $parts = explode('/', $this->ref);

        return $this->parameters->getParameter($parts[2]);
    }
}
