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
    private $responses;

    /**
     * @param Response[] $responses
     */
    public function __construct(array $responses)
    {
        $this->responses = $responses;
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
}
