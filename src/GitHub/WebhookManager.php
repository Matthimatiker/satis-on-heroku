<?php

namespace Matthimatiker\SatisOnHeroku\GitHub;

use Github\Api\Repository\Hooks;
use Matthimatiker\SatisOnHeroku\RepositoryUrl;

/**
 *
 */
class WebhookManager
{
    public function __construct(Hooks $hookApi, $webhookUrl)
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
