<?php

namespace WakeOnWeb\Component\Swagger\Specification;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class Paths
{
    /**
     * @var PathItem[]
     */
    private $paths;

    /**
     * @param PathItem[] $paths
     */
    public function __construct(array $paths)
    {
        $this->paths = $paths;
    }

    /**
     * @return PathItem[]
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * @param string $path
     *
     * @return PathItem
     */
    public function getPathItemFor($path)
    {
        if (array_key_exists($path, $this->paths) === true) {
            return $this->paths[$path];
        }
    }
}
