<?php

namespace WakeOnWeb\Swagger\Test;

use InvalidArgumentException;
use WakeOnWeb\Swagger\Specification\PathItem;
use WakeOnWeb\Swagger\Specification\Swagger;
use WakeOnWeb\Swagger\Test\Exception\ContentTypeException;
use WakeOnWeb\Swagger\Test\Exception\StatusCodeException;
use WakeOnWeb\Swagger\Test\Exception\SwaggerValidatorException;
use WakeOnWeb\Swagger\Test\Request\RequestInterface;
use WakeOnWeb\Swagger\Test\Response\ResponseInterface;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class SwaggerValidator
{
    /**
     * @var Swagger
     */
    private $swagger;

    /**
     * @var ResponseValidatorInterface[]
     */
    private $responseValidators = [];

    /**
     * @var RequestValidatorInterface[]
     */
    private $requestValidators = [];

    /**
     * @param Swagger $swagger
     */
    public function __construct(Swagger $swagger)
    {
        $this->swagger = $swagger;
    }

    /**
     * @param ResponseValidatorInterface $responseValidator
     */
    public function registerResponseValidator(ResponseValidatorInterface $responseValidator)
    {
        $this->responseValidators[] = $responseValidator;
    }


    /**
     * @param RequestValidatorInterface $requestValidator
     */
    public function registerRequestValidator(RequestValidatorInterface $requestValidator)
    {
        $this->requestValidators[] = $requestValidator;
    }

    /**
     * Validates the given response against the current Swagger file. It will check that the status code is the one
     * we expects, the `Content-Type` of the response and eventually the structure of the content.
     *
     * @param ResponseInterface $actual The actual response to check.
     * @param string            $method The method of the endpoint to check.
     * @param string            $path   The path of the endpoint to check.
     * @param int               $code   The status code of the endpoint to check.
     *
     * @throws InvalidArgumentException  When the given `$method` is not one of the `PathItem::METHOD_*` constant value.
     * @throws SwaggerValidatorException When The response does not validate the specification.
     */
    public function validateResponseFor(ResponseInterface $actual, $method, $path, $code)
    {
        $pathItem = $this
            ->swagger
            ->getPaths()
            ->getPathItemFor($path)
        ;

        switch ($method) {
            case PathItem::METHOD_GET:
                $operation = $pathItem->getGet();

                break;

            case PathItem::METHOD_PUT:
                $operation = $pathItem->getPut();

                break;

            case PathItem::METHOD_POST:
                $operation = $pathItem->getPost();

                break;

            case PathItem::METHOD_DELETE:
                $operation = $pathItem->getDelete();

                break;

            case PathItem::METHOD_OPTIONS:
                $operation = $pathItem->getOptions();

                break;

            case PathItem::METHOD_HEAD:
                $operation = $pathItem->getHead();

                break;

            case PathItem::METHOD_PATCH:
                $operation = $pathItem->getPatch();

                break;

            default:
                throw new InvalidArgumentException(
                    'The $method argument should be one of the PathItem::METHOD_* constant value.'
                );
        }

        $response = $operation
            ->getResponses()
            ->getResponseFor($code)
        ;

        if ($actual->getStatusCode() !== $code) {
            throw StatusCodeException::fromInvalidStatusCode($code, $actual->getStatusCode());
        }

        // @todo: Inherit.
        $produces = array_unique(array_merge($this->swagger->getProduces(), $operation->getProduces()));

        if ($actual->getContentType() && !in_array($actual->getContentType(), $produces)) {
            throw ContentTypeException::fromInvalidContentType($actual->getContentType(), $produces);
        }

        foreach ($this->responseValidators as $validator) {
            $validator->validateResponse($response, $actual);
        }
    }

    /**
     * Validates the given request against the current Swagger file. It will check that the request satisfies the
     * parameters we expects, the headers of the request and eventually the structure of the content.
     *
     * @param RequestInterface $actual The actual request to check.
     * @param string           $method The method of the endpoint to check.
     * @param string           $path   The path of the endpoint to check.
     *
     * @throws InvalidArgumentException  When the given `$method` is not one of the `PathItem::METHOD_*` constant value.
     * @throws SwaggerValidatorException When The request does not validate the specification.
     */
    public function validateRequestFor(RequestInterface $actual, $method, $path)
    {
        $pathItem = $this
            ->swagger
            ->getPaths()
            ->getPathItemFor($path)
        ;

        switch ($method) {
            case PathItem::METHOD_GET:
                $operation = $pathItem->getGet();

                break;

            case PathItem::METHOD_PUT:
                $operation = $pathItem->getPut();

                break;

            case PathItem::METHOD_POST:
                $operation = $pathItem->getPost();

                break;

            case PathItem::METHOD_DELETE:
                $operation = $pathItem->getDelete();

                break;

            case PathItem::METHOD_OPTIONS:
                $operation = $pathItem->getOptions();

                break;

            case PathItem::METHOD_HEAD:
                $operation = $pathItem->getHead();

                break;

            case PathItem::METHOD_PATCH:
                $operation = $pathItem->getPatch();

                break;

            default:
                throw new InvalidArgumentException(
                    'The $method argument should be one of the PathItem::METHOD_* constant value.'
                );
        }

        foreach ($this->requestValidators as $validator) {
            $validator->validateRequest($operation, $actual);
        }
    }
}
