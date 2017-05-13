<?php

namespace WakeOnWeb\Swagger\Test\Exception;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class StatusCodeException extends SwaggerValidatorException
{
    /**
     * @param int $expected
     * @param int $actual
     *
     * @return StatusCodeException
     */
    public static function fromInvalidStatusCode($expected, $actual)
    {
        return new self(sprintf(
            'The response status code is invalid. The code %d was expected, got %d.',
            $expected,
            $actual
        ));
    }
}