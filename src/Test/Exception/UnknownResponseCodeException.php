<?php

namespace WakeOnWeb\Component\Swagger\Test\Exception;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class UnknownResponseCodeException extends SwaggerValidatorException
{
    /**
     * @param int $code
     *
     * @return SwaggerValidatorException
     */
    public static function fromUnknownStatusCode($code)
    {
        return new self(sprintf(
            'The response status code is unknown to the schema. Got %d.',
            $code
        ));
    }
}
