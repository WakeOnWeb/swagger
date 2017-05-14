<?php

namespace WakeOnWeb\Swagger\Specification;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class ParametersDefinitions
{
    /**
     * @var Parameter[]
     */
    private $parameters;

    /**
     * @param Parameter[] $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @param string $name
     *
     * @return Parameter
     */
    public function getParameter($name)
    {
        return $this->parameters[$name];
    }
}
