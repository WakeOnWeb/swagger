<?php

namespace WakeOnWeb\Swagger\Specification;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class ResponsesDefinitions
{
    /**
     * @var Response[]
     */
    private $responses = [];

    /**
     * @param Response[] $definitions
     */
    public function setDefinitions(array $definitions)
    {
        $this->responses = $definitions;
    }

    /**
     * @param string $name
     *
     * @return Response
     */
    public function getResponse($name)
    {
        return $this->responses[$name];
    }

    /**
     * @return Response[]
     */
    public function getResponses()
    {
        return $this->responses;
    }
}
