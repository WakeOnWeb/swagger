<?php

namespace WakeOnWeb\Component\Swagger\Specification;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class BodyParameter extends AbstractParameter
{
    /**
     * @var Schema
     */
    private $schema;

    /**
     * @param string      $name
     * @param string      $in
     * @param string|null $description
     * @param bool        $required
     * @param Schema      $schema
     */
    public function __construct($name, $in, $description = null, $required, Schema $schema) //@codingStandardsIgnoreLine
    {
        parent::__construct($name, $in, $description, $required);

        $this->schema = $schema;
    }

    /**
     * @return Schema
     */
    public function getSchema()
    {
        return $this->schema;
    }
}
