<?php

namespace WakeOnWeb\Component\Swagger\Loader;

use WakeOnWeb\Component\Swagger\Loader\Exception\LoaderException;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class JsonLoader implements LoaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function load($filename)
    {
        $content = file_get_contents($filename);

        if ($content === false) {
            throw LoaderException::fromFilename($filename);
        }

        $spec = json_decode($content, true);

        if ($spec === null) {
            throw LoaderException::fromParser($filename, json_last_error_msg());
        }

        return $spec;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($filename)
    {
        return pathinfo($filename, PATHINFO_EXTENSION) === 'json';
    }
}
