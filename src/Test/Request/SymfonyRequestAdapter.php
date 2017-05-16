<?php

namespace WakeOnWeb\Component\Swagger\Test\Request;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class SymfonyRequestAdapter implements RequestInterface
{
    /**
     * @var string
     */
    private $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return $this->request->headers->get('Content-Type', null, true);
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return $this->request->getContent();
    }
}
