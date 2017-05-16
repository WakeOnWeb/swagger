<?php

namespace WakeOnWeb\Component\Swagger\Test;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use WakeOnWeb\Component\Swagger\Specification\Operation;
use WakeOnWeb\Component\Swagger\Specification\Response;

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
            foreach ($actual->getHeader('Content-Type') as $contentType) {
                if ($contentValidator->support($contentType)) {
                    $contentValidator->validateContent($schema, $actual);
                }
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
            foreach ($actual->getHeader('Content-Type') as $contentType) {
                if ($contentValidator->support($contentType)) {
                    $contentValidator->validateContent($schema, $actual);
                }
            }
        }
    }
}
