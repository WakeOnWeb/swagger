<?php

namespace WakeOnWeb\Component\Swagger\Specification;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class Items
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $format;

    /**
     * @var Items|null
     */
    private $items;

    /**
     * @var string
     */
    private $collectionFormat;

    /**
     * @var mixed
     */
    private $default;

    /**
     * @var int
     */
    private $maximum;

    /**
     * @var bool
     */
    private $exclusiveMaximum;

    /**
     * @var int
     */
    private $minimum;

    /**
     * @var bool
     */
    private $exclusiveMinimum;

    /**
     * @var int
     */
    private $maxLength;

    /**
     * @var int
     */
    private $minLength;

    /**
     * @var string
     */
    private $pattern;

    /**
     * @var int
     */
    private $maxItems;

    /**
     * @var int
     */
    private $minItems;

    /**
     * @var bool
     */
    private $uniqueItems;

    /**
     * @var array|null
     */
    private $enum;

    /**
     * @var int
     */
    private $multipleOf;

    /**
     * @param string      $type
     * @param string      $format
     * @param Items|null  $items
     * @param string      $collectionFormat
     * @param mixed       $default
     * @param int         $maximum
     * @param bool        $exclusiveMaximum
     * @param int         $minimum
     * @param bool        $exclusiveMinimum
     * @param int         $maxLength
     * @param int         $minLength
     * @param string      $pattern
     * @param int         $maxItems
     * @param int         $minItems
     * @param bool        $uniqueItems
     * @param array|null  $enum
     * @param int         $multipleOf
     */
    public function __construct($type, $format, Items $items = null, $collectionFormat, $default, $maximum, $exclusiveMaximum, $minimum, $exclusiveMinimum, $maxLength, $minLength, $pattern, $maxItems, $minItems, $uniqueItems, array $enum = null, $multipleOf)
    {
        $this->type = $type;
        $this->format = $format;
        $this->items = $items;
        $this->collectionFormat = $collectionFormat;
        $this->default = $default;
        $this->maximum = $maximum;
        $this->exclusiveMaximum = $exclusiveMaximum;
        $this->minimum = $minimum;
        $this->exclusiveMinimum = $exclusiveMinimum;
        $this->maxLength = $maxLength;
        $this->minLength = $minLength;
        $this->pattern = $pattern;
        $this->maxItems = $maxItems;
        $this->minItems = $minItems;
        $this->uniqueItems = $uniqueItems;
        $this->enum = $enum;
        $this->multipleOf = $multipleOf;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @return Items|null
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return string
     */
    public function getCollectionFormat()
    {
        return $this->collectionFormat;
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @return int
     */
    public function getMaximum()
    {
        return $this->maximum;
    }

    /**
     * @return boolean
     */
    public function isExclusiveMaximum()
    {
        return $this->exclusiveMaximum;
    }

    /**
     * @return int
     */
    public function getMinimum()
    {
        return $this->minimum;
    }

    /**
     * @return boolean
     */
    public function isExclusiveMinimum()
    {
        return $this->exclusiveMinimum;
    }

    /**
     * @return int
     */
    public function getMaxLength()
    {
        return $this->maxLength;
    }

    /**
     * @return int
     */
    public function getMinLength()
    {
        return $this->minLength;
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @return int
     */
    public function getMaxItems()
    {
        return $this->maxItems;
    }

    /**
     * @return int
     */
    public function getMinItems()
    {
        return $this->minItems;
    }

    /**
     * @return boolean
     */
    public function hasUniqueItems()
    {
        return $this->uniqueItems;
    }

    /**
     * @return array|null
     */
    public function getEnum()
    {
        return $this->enum;
    }

    /**
     * @return int
     */
    public function getMultipleOf()
    {
        return $this->multipleOf;
    }
}
