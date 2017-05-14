<?php

namespace WakeOnWeb\Swagger\Specification;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class Info
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $termsOfService;

    /**
     * @var Contact|null
     */
    private $contact;

    /**
     * @var License|null
     */
    private $license;

    /**
     * @var string
     */
    private $version;

    /**
     * Constructor.
     *
     * @param string       $title
     * @param string       $description
     * @param string       $termsOfService
     * @param Contact|null $contact
     * @param License|null $license
     * @param string       $version
     */
    public function __construct($title, $description, $termsOfService, Contact $contact = null, License $license = null, $version)
    {
        $this->title = $title;
        $this->description = $description;
        $this->termsOfService = $termsOfService;
        $this->contact = $contact;
        $this->license = $license;
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
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
    public function getTermsOfService()
    {
        return $this->termsOfService;
    }

    /**
     * @return Contact|null
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @return License|null
     */
    public function getLicense()
    {
        return $this->license;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }
}
