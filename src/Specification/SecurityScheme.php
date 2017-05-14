<?php

namespace WakeOnWeb\Swagger\Specification;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class SecurityScheme
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $in;

    /**
     * @var string
     */
    private $flow;

    /**
     * @var string
     */
    private $authorizationUrl;

    /**
     * @var string
     */
    private $tokenUrl;

    /**
     * @var Scopes
     */
    private $scopes;

    /**
     * Constructor.
     *
     * @param string $type
     * @param string $description
     * @param string $name
     * @param string $in
     * @param string $flow
     * @param string $authorizationUrl
     * @param string $tokenUrl
     * @param Scopes $scopes
     */
    public function __construct($type, $description, $name, $in, $flow, $authorizationUrl, $tokenUrl, Scopes $scopes)
    {
        $this->type = $type;
        $this->description = $description;
        $this->name = $name;
        $this->in = $in;
        $this->flow = $flow;
        $this->authorizationUrl = $authorizationUrl;
        $this->tokenUrl = $tokenUrl;
        $this->scopes = $scopes;
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
    public function getDescription()
    {
        return $this->description;
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
     * @return string
     */
    public function getFlow()
    {
        return $this->flow;
    }

    /**
     * @return string
     */
    public function getAuthorizationUrl()
    {
        return $this->authorizationUrl;
    }

    /**
     * @return string
     */
    public function getTokenUrl()
    {
        return $this->tokenUrl;
    }

    /**
     * @return Scopes
     */
    public function getScopes()
    {
        return $this->scopes;
    }
}
