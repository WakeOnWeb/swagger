<?php

namespace WakeOnWeb\Component\Swagger\Test;

use WakeOnWeb\Component\Swagger\Specification\Operation;
use WakeOnWeb\Component\Swagger\Specification\Response;
use WakeOnWeb\Component\Swagger\Test\Request\RequestInterface;
use WakeOnWeb\Component\Swagger\Test\Response\ResponseInterface;

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

        foreach ($this->contentValidators as $contentValidator) {
            if ($contentValidator->support($actual->getContentType())) {
                $contentValidator->validateContent($schema, $actual->getBody());
            }
        }
    }
}
