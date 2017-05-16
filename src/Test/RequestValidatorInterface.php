<?php

namespace WakeOnWeb\Component\Swagger\Test;

use WakeOnWeb\Component\Swagger\Specification\Operation;
use WakeOnWeb\Component\Swagger\Test\Exception\SwaggerValidatorException;
use WakeOnWeb\Component\Swagger\Test\Request\RequestInterface;

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
