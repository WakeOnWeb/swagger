<?php

namespace WakeOnWeb\Component\Swagger\Specification;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class ResponseReference
{
    /**
     * @var string
     */
    private $ref;

    /**
     * @var ResponsesDefinitions
     */
    private $responses;

    /**
     * @param string               $ref
     * @param ResponsesDefinitions $responses
     */
    public function __construct($ref, ResponsesDefinitions $responses)
    {
        $this->ref = $ref;
        $this->responses = $responses;
    }

    /**
     * @return Response
     */
    public function resolve()
    {
        $parts = explode('/', $this->ref);

        return $this->responses->getResponse($parts[2]);
    }
}
