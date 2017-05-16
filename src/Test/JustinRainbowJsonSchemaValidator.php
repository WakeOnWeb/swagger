<?php

namespace WakeOnWeb\Component\Swagger\Test;

use JsonSchema\Constraints\Factory;
use JsonSchema\SchemaStorage;
use JsonSchema\Validator;
use Psr\Http\Message\MessageInterface;
use WakeOnWeb\Component\Swagger\Specification\Schema;
use WakeOnWeb\Component\Swagger\Test\Exception\JsonSchemaException;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class JustinRainbowJsonSchemaValidator implements ContentValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function validateContent(Schema $schema, MessageInterface $actual)
    {
        $schema = json_decode($schema->getJsonSchemaAsJson());
        $content = json_decode($actual->getBody());

        $schemaStorage = new SchemaStorage();
        $schemaStorage->addSchema('file://json-schema', $schema);

        $validator = new Validator(new Factory($schemaStorage));

        $validator->validate($content, $schema);

        if (!$validator->isValid()) {
            throw JsonSchemaException::fromValidationErrors(array_map(
                function (array $error) {
                    return $error['message'];
                },
                $validator->getErrors()
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
