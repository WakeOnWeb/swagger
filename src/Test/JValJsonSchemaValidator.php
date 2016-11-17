<?php

namespace UCS\Swagger\Test;

use org\bovigo\vfs\vfsStream as Stream;
use org\bovigo\vfs\vfsStreamDirectory as Directory;
use org\bovigo\vfs\vfsStreamFile as File;
use JVal\Validator;
use UCS\Swagger\Specification\Response;
use UCS\Swagger\Test\Exception\JsonSchemaException;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class JValJsonSchemaValidator implements ContentValidatorInterface
{
    /**
     * @var Directory
     */
    private $directory;

    /**
     * @var Validator
     */
    private $validator;

    /**
     *
     */
    public function __construct()
    {
        $this->directory = Stream::setup('swagger');
        $this->validator = Validator::buildDefault();
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

        $violations = $this->validator->validate(json_decode($content), json_decode($schema), sprintf('vfs://swagger/%s', $filename));

        if (count($violations)) {
            throw JsonSchemaException::fromValidationErrors(array_map(
                function ($violation) {
                    return sprintf('%s: %s', $violation['path'], $violation['message']);
                },
                $violations
            ));
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
