<?php

namespace WakeOnWeb\Component\Swagger\Test\Exception;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class UnknownPathException extends SwaggerValidatorException
{
    /**
     * @param int $path
     *
     * @return SwaggerValidatorException
     */
    public static function fromUnknownPath($path)
    {
        return new self(sprintf('The request path is unknown to the schema. Got %d.', $path));
    }
}
