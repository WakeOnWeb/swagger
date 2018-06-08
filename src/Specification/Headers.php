<?php

namespace WakeOnWeb\Component\Swagger\Specification;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class Headers
{
    /**
     * @var Header[]
     */
    private $headers = [];

    /**
     * Constructor.
     *
     * @param Header[] $headers
     */
    public function __construct(array $headers)
    {
        foreach ($headers as $name => $header) {
            $this->headers[strtolower($name)] = $header;
        }
    }

    /**
     * @param string $name
     *
     * @return Header
     */
    public function getHeader($name)
    {
        return $this->headers[strtolower($name)];
    }
}
