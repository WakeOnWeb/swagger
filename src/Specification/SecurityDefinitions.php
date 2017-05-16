<?php

namespace WakeOnWeb\Component\Swagger\Specification;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class SecurityDefinitions
{
    /**
     * @var SecurityScheme[]
     */
    private $definitions;

    /**
     * @param SecurityScheme[] $definitions
     */
    public function __construct(array $definitions)
    {
        $this->definitions = $definitions;
    }

    /**
     * @param string $name
     *
     * @return SecurityScheme
     */
    public function getSecurityScheme($name)
    {
        return $this->definitions[$name];
    }
}
