<?php

namespace WakeOnWeb\Component\Swagger\Test\Response;

use Symfony\Component\HttpFoundation\Response;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class SymfonyResponseAdapter implements ResponseInterface
{
    /**
     * @var string
     */
    private $response;

    /**
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return $this->response->headers->get('Content-Type', null, true);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return $this->response->getContent();
    }
}
