<?php

namespace WakeOnWeb\Swagger\Specification;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class PathItem
{
    const METHOD_GET = 'GET';
    const METHOD_PUT = 'PUT';
    const METHOD_POST = 'POST';
    const METHOD_DELETE = 'DELETE';
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_HEAD = 'HEAD';
    const METHOD_PATCH = 'PATCH';

    /**
     * @var Operation|null
     */
    private $get;

    /**
     * @var Operation|null
     */
    private $put;

    /**
     * @var Operation|null
     */
    private $post;

    /**
     * @var Operation|null
     */
    private $delete;

    /**
     * @var Operation|null
     */
    private $options;

    /**
     * @var Operation|null
     */
    private $head;

    /**
     * @var Operation|null
     */
    private $patch;

    /**
     * @var BodyAbstractParameter[]|Reference[]
     */
    private $parameters;

    /**
     * PathItem constructor.
     *
     * @param Operation|null                      $get
     * @param Operation|null                      $put
     * @param Operation|null                      $post
     * @param Operation|null                      $delete
     * @param Operation|null                      $options
     * @param Operation|null                      $head
     * @param Operation|null                      $patch
     * @param BodyAbstractParameter[]|Reference[] $parameters
     */
    public function __construct(Operation $get = null, Operation $put = null, Operation $post = null, Operation $delete = null, Operation $options = null, Operation $head = null, Operation $patch = null, array $parameters)
    {
        $this->get = $get;
        $this->put = $put;
        $this->post = $post;
        $this->delete = $delete;
        $this->options = $options;
        $this->head = $head;
        $this->patch = $patch;
        $this->parameters = $parameters;
    }

    /**
     * @return Operation|null
     */
    public function getGet()
    {
        return $this->get;
    }

    /**
     * @return Operation|null
     */
    public function getPut()
    {
        return $this->put;
    }

    /**
     * @return Operation|null
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @return Operation|null
     */
    public function getDelete()
    {
        return $this->delete;
    }

    /**
     * @return Operation|null
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return Operation|null
     */
    public function getHead()
    {
        return $this->head;
    }

    /**
     * @return Operation|null
     */
    public function getPatch()
    {
        return $this->patch;
    }

    /**
     * @return BodyAbstractParameter[]|Reference[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
