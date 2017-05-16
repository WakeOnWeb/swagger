<?php

namespace WakeOnWeb\Swagger\Test;

use WakeOnWeb\Swagger\Specification\Schema;
use WakeOnWeb\Swagger\Test\Exception\ContentValidatorException;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
interface ContentValidatorInterface
{
    /**
     * @param Schema $schema
     * @param string $content
     *
     * @throws ContentValidatorException
     */
    public function validateContent(Schema $schema, $content);

    /**
     * @param string $mimeType
     *
     * @return bool
     */
    public function support($mimeType);
}
