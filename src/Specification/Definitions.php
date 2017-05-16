<?php

namespace WakeOnWeb\Component\Swagger\Specification;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class Definitions
{
    /**
     * @var Schema[]
     */
    private $definitions = [];

    /**
     * @param Schema[] $definitions
     */
    public function setDefinitions(array $definitions)
    {
        $this->definitions = $definitions;
    }

    /**
     * @return Schema[]
     */
    public function getDefinitions()
    {
        return $this->definitions;
    }
}
