<?php

namespace WakeOnWeb\Swagger\Test;

use WakeOnWeb\Swagger\Specification\Operation;
use WakeOnWeb\Swagger\Test\Exception\SwaggerValidatorException;
use WakeOnWeb\Swagger\Test\Request\RequestInterface;

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
