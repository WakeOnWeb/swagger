<?php

namespace WakeOnWeb\Swagger\Specification;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class Scopes
{
    /**
     * @var string[]
     */
    private $scopes;

    /**
     * @param string[] $scopes
     */
    public function __construct(array $scopes)
    {
        $this->scopes = $scopes;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasScope($name)
    {
        return isset($this->scopes[$name]);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function getScopeDescription($name)
    {
        return $this->scopes[$name];
    }
}
