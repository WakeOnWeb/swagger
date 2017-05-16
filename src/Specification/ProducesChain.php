<?php

namespace WakeOnWeb\Component\Swagger\Specification;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class ProducesChain
{
    /**
     * @var ProducesChain|null
     */
    private $prev;

    /**
     * @var string[]
     */
    private $produces = [];

    /**
     * @param ProducesChain|null $prev
     */
    public function __construct(ProducesChain $prev = null)
    {
        $this->prev = $prev;
    }

    /**
     * @param string[] $produces
     */
    public function setProduces(array $produces)
    {
        $this->produces = $produces;
    }

    /**
     * @return string[]
     */
    public function getProduces()
    {
        $produces = $this->produces;

        if ($this->prev !== null) {
            $produces = array_merge($this->prev->getProduces(), $produces);
        }

        return array_unique($produces);
    }
}
