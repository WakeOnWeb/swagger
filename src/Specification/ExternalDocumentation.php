<?php

namespace WakeOnWeb\Component\Swagger\Specification;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class ExternalDocumentation
{
    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $url;

    /**
     * @param string $description
     * @param string $url
     */
    public function __construct($description, $url)
    {
        $this->description = $description;
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}
