<?php

namespace WakeOnWeb\Component\Swagger\Specification;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class Response
{
    /**
     * @var string
     */
    private $description;

    /**
     * @var Schema|null
     */
    private $schema;

    /**
     * @var Headers|null
     */
    private $headers;

    /**
     * @var Examples|null
     */
    private $examples;

    /**
     * @param string        $description
     * @param Schema|null   $schema
     * @param Headers|null  $headers
     * @param Examples|null $examples
     */
    public function __construct($description, Schema $schema = null, Headers $headers = null, Examples $examples = null)
    {
        $this->description = $description;
        $this->schema = $schema;
        $this->headers = $headers;
        $this->examples = $examples;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return Schema|null
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @return Headers|null
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return Examples|null
     */
    public function getExamples()
    {
        return $this->examples;
    }
}
