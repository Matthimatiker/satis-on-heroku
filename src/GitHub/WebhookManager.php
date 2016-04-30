<?php

namespace Matthimatiker\SatisOnHeroku\GitHub;

use Github\Api\Repository\Hooks;
use Guzzle\Http\Url;
use Matthimatiker\SatisOnHeroku\RepositoryUrl;

/**
 * Manages GitHub push hooks for repositories.
 */
class WebhookManager
{
    /**
     * @param Hooks $hookApi
     * @param Url $webhookUrl
     */
    public function __construct(Hooks $hookApi, Url $webhookUrl)
    {
    }

    /**
     * @param RepositoryUrl $url
     * @return boolean
     */
    public function supports(RepositoryUrl $url)
    {

    }

    /**
     * @param RepositoryUrl $url
     */
    public function registerFor(RepositoryUrl $url)
    {

    }
}
