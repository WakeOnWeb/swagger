<?php

namespace WakeOnWeb\Component\Swagger\Specification;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class Examples
{
    /**
     * @var array
     */
    private $examples;

    /**
     * @param array $examples
     */
    public function __construct(array $examples)
    {
        $this->examples = $examples;
    }

    /**
     * @return array
     */
    public function getExamples()
    {
        return $this->examples;
    }

    /**
     * @param string $mimeType
     *
     * @return mixed
     */
    public function getExampleFor($mimeType)
    {
        return $this->examples[$mimeType];
    }
}
