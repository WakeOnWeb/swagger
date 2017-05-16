<?php

namespace WakeOnWeb\Swagger\Specification;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class ParametersChain
{
    /**
     * @var ParametersChain|null
     */
    private $prev;

    /**
     * @var AbstractParameter[]
     */
    private $parameters = [];

    /**
     * @param ParametersChain|null $prev
     */
    public function __construct(ParametersChain $prev = null)
    {
        $this->prev = $prev;
    }

    /**
     * @param AbstractParameter[] $parameters
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return AbstractParameter[]
     */
    public function getParameters()
    {
        $parameters = $this->parameters;

        if ($this->prev !== null) {
            $parameters = array_merge($this->prev->getParameters(), $parameters);
        }

        foreach ($parameters as &$parameter) {
            if ($parameter instanceof ParameterReference) {
                $parameter = $parameter->resolve();
            }
        }

        return $parameters;
    }
}
