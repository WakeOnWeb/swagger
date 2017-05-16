<?php

namespace WakeOnWeb\Component\Swagger\Specification;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class ConsumesChain
{
    /**
     * @var ConsumesChain|null
     */
    private $prev;

    /**
     * @var string[]
     */
    private $consumes = [];

    /**
     * @param ConsumesChain|null $prev
     */
    public function __construct(ConsumesChain $prev = null)
    {
        $this->prev = $prev;
    }

    /**
     * @param string[] $consumes
     */
    public function setConsumes(array $consumes)
    {
        $this->consumes = $consumes;
    }

    /**
     * @return string[]
     */
    public function getConsumes()
    {
        $consumes = $this->consumes;

        if ($this->prev !== null) {
            $consumes = array_merge($this->prev->getConsumes(), $consumes);
        }

        return array_unique($consumes);
    }
}
