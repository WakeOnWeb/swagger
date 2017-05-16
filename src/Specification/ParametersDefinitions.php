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
    private $parameters = [];

    /**
     * @param Parameter[] $definitions
     */
    public function setDefinitions(array $definitions)
    {
        $this->parameters = $definitions;
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
