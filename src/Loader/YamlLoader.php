<?php

namespace WakeOnWeb\Swagger\Loader;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use WakeOnWeb\Swagger\Loader\Exception\LoaderException;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class YamlLoader implements LoaderInterface
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

        try {
            return Yaml::parse($content);
        } catch (ParseException $e) {
            throw LoaderException::fromParser($filename, $e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($filename)
    {
        return in_array(pathinfo($filename, PATHINFO_EXTENSION), ['yml', 'yaml']);
    }
}