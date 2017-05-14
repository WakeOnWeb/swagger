<?php

namespace WakeOnWeb\Swagger\Test;

use WakeOnWeb\Swagger\Specification\Operation;
use WakeOnWeb\Swagger\Specification\Response;
use WakeOnWeb\Swagger\Test\Request\RequestInterface;
use WakeOnWeb\Swagger\Test\Response\ResponseInterface;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class ContentValidator implements ResponseValidatorInterface, RequestValidatorInterface
{
    /**
     * @var ContentValidatorInterface[]
     */
    private $contentValidators = [];

    /**
     * @param ContentValidatorInterface $contentValidator
     */
    public function registerContentValidator(ContentValidatorInterface $contentValidator)
    {
        $this->contentValidators[] = $contentValidator;
    }

    /**
     * {@inheritdoc}
     */
    public function validateResponse(Response $response, ResponseInterface $actual)
    {
        $schema = $response->getSchema();

        if (!$schema) {
            return;
        }

        // @todo: Handle the case where the schema is an XSD.
        $schema = $schema->getJsonSchemaAsJson();

        foreach ($this->contentValidators as $contentValidator) {
            if ($contentValidator->support($actual->getContentType())) {
                $contentValidator->validateContent($schema, $actual->getBody());
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateRequest(Operation $operation, RequestInterface $actual)
    {
        $parameter = $operation->getBodyParameter();

        if (!$parameter) {
            return;
        }

        $schema = $parameter->getSchema();

        if (!$schema) {
            return;
        }

        // @todo: Handle the case where the schema is an XSD.
        $schema = $schema->getJsonSchemaAsJson();

        foreach ($this->contentValidators as $contentValidator) {
            if ($contentValidator->support($actual->getContentType())) {
                $contentValidator->validateContent($schema, $actual->getBody());
            }
        }
    }
}
