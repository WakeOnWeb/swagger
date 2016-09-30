<?php

namespace UCS\Swagger\Specification;

use JsonSerializable;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class Definitions implements JsonSerializable
{
    /**
     * @var array
     */
    private $definitions;

    /**
     * @param array $definitions
     */
    public function __construct(array $definitions)
    {
        $this->definitions = $definitions;
    }

    /**
     * @return array
     */
    public function getDefinitions()
    {
        return $this->definitions;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->definitions;
    }
}