<?php

namespace WakeOnWeb\Swagger\Test\Request;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
interface RequestInterface
{
    /**
     * @return string|null
     */
    public function getContentType();

    /**
     * @return string
     */
    public function getBody();
}
