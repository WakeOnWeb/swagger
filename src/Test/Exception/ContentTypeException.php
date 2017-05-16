<?php

namespace WakeOnWeb\Component\Swagger\Test\Exception;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class ContentTypeException extends SwaggerValidatorException
{
    /**
     * @param string[] $actual
     * @param string[] $expected
     *
     * @return ContentTypeException
     */
    public static function fromInvalidContentType(array $actual, array $expected)
    {
        return new self(sprintf(
            'The response "Content-Type" is invalid. The type(s) %s is/are not one of the allowed types (%s).',
            implode(', ', $actual),
            implode(', ', $expected)
        ));
    }
}
