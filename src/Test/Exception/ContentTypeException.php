<?php

namespace WakeOnWeb\Component\Swagger\Test\Exception;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class ContentTypeException extends SwaggerValidatorException
{
    /**
     * @param string $actual
     * @param array  $expected
     *
     * @return ContentTypeException
     */
    public static function fromInvalidContentType($actual, array $expected)
    {
        return new self(sprintf(
            'The response "Content-Type" is invalid. The type "%s" is not one of the allowed types (%s).',
            $actual,
            implode(', ', $expected)
        ));
    }
}
