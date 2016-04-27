<?php

namespace Matthimatiker\SatisOnHeroku;

/**
 * Represents a repository URL.
 */
class RepositoryUrl
{
    /**
     * The original URL.
     *
     * @var string
     */
    protected $url = null;

    /**
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return parse_url($this->url, PHP_URL_HOST);
    }

    /**
     * @return string The original URL.
     */
    public function __toString()
    {
        return $this->url;
    }
}
