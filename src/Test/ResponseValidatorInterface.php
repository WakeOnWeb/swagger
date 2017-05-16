<?php

namespace WakeOnWeb\Component\Swagger\Test;

use Psr\Http\Message\ResponseInterface;
use WakeOnWeb\Component\Swagger\Specification\Response;
use WakeOnWeb\Component\Swagger\Test\Exception\SwaggerValidatorException;

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
