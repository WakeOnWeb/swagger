<?php

namespace WakeOnWeb\Component\Swagger\Test;

use WakeOnWeb\Component\Swagger\Specification\Schema;
use WakeOnWeb\Component\Swagger\Test\Exception\ContentValidatorException;

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
