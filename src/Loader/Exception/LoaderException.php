<?php

namespace WakeOnWeb\Swagger\Loader\Exception;

use InvalidArgumentException;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class LoaderException extends InvalidArgumentException
{
    /**
     * @param string $filename
     *
     * @return LoaderException
     */
    public static function fromFilename($filename)
    {
        return new self(sprintf('The filename "%s" could not be loaded.', $filename));
    }

    /**
     * @param string $filename
     * @param string $message
     *
     * @return LoaderException
     */
    public static function fromParser($filename, $message)
    {
        return new self(sprintf('The filename "%s" leads to a parsing issue: %s', $filename, $message));
    }
}