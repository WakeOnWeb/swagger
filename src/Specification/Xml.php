<?php

namespace WakeOnWeb\Component\Swagger\Specification;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class Xml
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var bool
     */
    private $attribute;

    /**
     * @var bool
     */
    private $wrapped;

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $namespace
     * @param string $prefix
     * @param bool   $attribute
     * @param bool   $wrapped
     */
    public function __construct($name, $namespace, $prefix, $attribute, $wrapped)
    {
        $this->name = $name;
        $this->namespace = $namespace;
        $this->prefix = $prefix;
        $this->attribute = $attribute;
        $this->wrapped = $wrapped;
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
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @return bool
     */
    public function isAttribute()
    {
        return $this->attribute;
    }

    /**
     * @return bool
     */
    public function isWrapped()
    {
        return $this->wrapped;
    }
}
