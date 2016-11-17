<?php

namespace UCS\Swagger\Test;

use org\bovigo\vfs\vfsStream as Stream;
use org\bovigo\vfs\vfsStreamDirectory as Directory;
use org\bovigo\vfs\vfsStreamFile as File;
use UCS\Swagger\Specification\Response;
use UCS\Swagger\Test\Exception\JsonSchemaException;
use Webmozart\Json\JsonValidator;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class JustinRainbowJsonSchemaValidator implements ContentValidatorInterface
{
    /**
     * @var Directory
     */
    private $directory;

    /**
     * @var JsonValidator
     */
    private $jsonValidator;

    /**
     *
     */
    public function __construct()
    {
        $this->directory = Stream::setup('swagger');
        $this->jsonValidator = new JsonValidator();
    }

    /**
     * {@inheritdoc}
     */
    public function validateContent(Response $response, $content)
    {
        $schema = $response->getSchema();

        if (!$schema) {
            return;
        }

        $schema = $schema->getJsonSchemaAsJson();

        $filename = sprintf('%s.json', md5($schema));

        if (!$this->directory->hasChild($filename)) {
            $file = new File($filename);
            $file->setContent($schema);

            $this->directory->addChild($file);
        }

        $errors = $this
            ->jsonValidator
            ->validate(json_decode($content), sprintf('vfs://swagger/%s', $filename))
        ;

        if (count($errors)) {
            throw JsonSchemaException::fromValidationErrors($errors);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function support($mimeType)
    {
        return $mimeType === 'application/json';
    }
}
