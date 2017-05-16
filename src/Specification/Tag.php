<?php

namespace WakeOnWeb\Component\Swagger\Specification;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class Tag
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var ExternalDocumentation|null
     */
    private $externalDocs;

    /**
     * @param string                     $name
     * @param string                     $description
     * @param ExternalDocumentation|null $externalDocs
     */
    public function __construct($name, $description, ExternalDocumentation $externalDocs = null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->externalDocs = $externalDocs;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return ExternalDocumentation|null
     */
    public function getExternalDocs()
    {
        return $this->externalDocs;
    }
}
