<?php

namespace WakeOnWeb\Component\Swagger\Test;

use Psr\Http\Message\RequestInterface;
use WakeOnWeb\Component\Swagger\Specification\Operation;
use WakeOnWeb\Component\Swagger\Test\Exception\SwaggerValidatorException;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
interface RequestValidatorInterface
{
    /**
     * @param Operation        $operation
     * @param RequestInterface $actual
     *
     * @throws SwaggerValidatorException
     */
    public function validateRequest(Operation $operation, RequestInterface $actual);
}
