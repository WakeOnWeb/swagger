<?php

namespace WakeOnWeb\Component\Swagger\Test;

use Psr\Http\Message\MessageInterface;
use WakeOnWeb\Component\Swagger\Specification\Schema;
use WakeOnWeb\Component\Swagger\Test\Exception\ContentValidatorException;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
interface ContentValidatorInterface
{
    /**
     * @param Schema           $schema
     * @param MessageInterface $actual
     *
     * @throws ContentValidatorException
     */
    public function validateContent(Schema $schema, MessageInterface $actual);

    /**
     * @param string $mimeType
     *
     * @return bool
     */
    public function support($mimeType);
}
