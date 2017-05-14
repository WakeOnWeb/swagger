<?php

namespace WakeOnWeb\Swagger\Test;

use WakeOnWeb\Swagger\Specification\Response;
use WakeOnWeb\Swagger\Test\Exception\SwaggerValidatorException;
use WakeOnWeb\Swagger\Test\Response\ResponseInterface;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
interface ResponseValidatorInterface
{
    /**
     * @param Response          $response
     * @param ResponseInterface $actual
     *
     * @throws SwaggerValidatorException
     */
    public function validateResponse(Response $response, ResponseInterface $actual);
}
