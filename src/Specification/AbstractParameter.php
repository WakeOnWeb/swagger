<?php

namespace WakeOnWeb\Component\Swagger\Specification;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
abstract class AbstractParameter
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $in;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @var bool
     */
    private $required;

    /**
     * Constructor.
     *
     * @param string      $name
     * @param string      $in
     * @param string|null $description
     * @param bool        $required
     */
    public function __construct($name, $in, $description = null, $required)
    {
        $this->name = $name;
        $this->in = $in;
        $this->description = $description;
        $this->required = $required;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getIn()
    {
        return $this->in;
    }

    /**
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return $this->required;
    }
}
