<?php

namespace WakeOnWeb\Swagger\Test;

use WakeOnWeb\Swagger\Specification\Response;
use WakeOnWeb\Swagger\Test\Exception\ContentValidatorException;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
interface ContentValidatorInterface
{
    /**
     * @param Response $response
     * @param string   $content
     *
     * @throws ContentValidatorException
     */
    public function validateContent(Response $response, $content);

    /**
     * @param string $mimeType
     *
     * @return bool
     */
    public function support($mimeType);
}