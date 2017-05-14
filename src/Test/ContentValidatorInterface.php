<?php

namespace WakeOnWeb\Swagger\Test;

use WakeOnWeb\Swagger\Test\Exception\ContentValidatorException;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
interface ContentValidatorInterface
{
    /**
     * @param string $schema
     * @param string $content
     *
     * @throws ContentValidatorException
     */
    public function validateContent($schema, $content);

    /**
     * @param string $mimeType
     *
     * @return bool
     */
    public function support($mimeType);
}
