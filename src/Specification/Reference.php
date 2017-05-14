<?php

namespace WakeOnWeb\Swagger\Specification;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class Reference
{
    /**
     * @var string
     */
    private $ref;

    /**
     * @param string $ref
     */
    public function __construct($ref)
    {
        $this->ref = $ref;
    }
}
