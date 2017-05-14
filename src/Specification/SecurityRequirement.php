<?php

namespace WakeOnWeb\Swagger\Specification;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class SecurityRequirement
{
    /**
     * @var string[][]
     */
    private $schemes;

    /**
     * @param string[][] $schemes
     */
    public function __construct(array $schemes)
    {
        $this->schemes = $schemes;
    }

    /**
     * @param string $name
     *
     * @return string[]
     */
    public function getScheme($name)
    {
        return $this->schemes[$name];
    }
}
