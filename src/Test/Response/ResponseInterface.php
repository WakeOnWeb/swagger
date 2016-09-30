<?php

namespace UCS\Swagger\Test\Response;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
interface ResponseInterface
{
    /**
     * @return string|null
     */
    public function getContentType();

    /**
     * @return int
     */
    public function getStatusCode();

    /**
     * @return string
     */
    public function getBody();
}