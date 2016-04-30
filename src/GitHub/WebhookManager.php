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
     * @var Hooks
     */
    protected $hooksApi = null;

    /**
     * The URL to the webhook.
     *
     * @var Url
     */
    protected $hookUrl = null;

    /**
     * @param Hooks $hooksApi
     * @param Url $webhookUrl
     */
    public function __construct(Hooks $hooksApi, Url $webhookUrl)
    {
        $this->hooksApi = $hooksApi;
        $this->hookUrl = $webhookUrl;
    }

    /**
     * @param RepositoryUrl $url
     * @return boolean
     */
    public function supports(RepositoryUrl $url)
    {
        list($user, $repository) = $this->getUserAndRepositoryFrom($url);
        return $user !== null && $repository !== null;
    }

    /**
     * @param RepositoryUrl $url
     */
    public function registerFor(RepositoryUrl $url)
    {
        $activeHook = $this->findActiveSatisHook($url);
        if ($activeHook === null) {
            // Hook does not exist, create one.
            list($user, $repository) = $this->getUserAndRepositoryFrom($url);
            $this->hooksApi->create($user, $repository, $this->getHookDefinition());
            return;
        }
        if ((string)$this->hookUrl === $activeHook['config']['url']) {
            // Hook URL did not change, no update necessary.
            return;
        }
        list($user, $repository) = $this->getUserAndRepositoryFrom($url);
        $this->hooksApi->update($user, $repository, $activeHook['id'], $this->getHookDefinition());
    }

    /**
     * Returns the hook definition that can be used with the GitHub API.
     *
     * @return array<string=>mixed>
     */
    private function getHookDefinition()
    {
        return array(
            'name' => 'web',
            'config' => array(
                'url' => (string)$this->hookUrl,
                'content_type' => 'json',
                'insecure_ssl' => '0'
            )
        );
    }

    /**
     * @param RepositoryUrl $url
     * @return array<string, mixed>|null
     */
    private function findActiveSatisHook(RepositoryUrl $url)
    {
        list($user, $repository) = $this->getUserAndRepositoryFrom($url);
        $alreadyRegisteredHooks = $this->hooksApi->all($user, $repository);
        foreach ($alreadyRegisteredHooks as $definition) {
            /* @var $definition array<string, mixed> */
            if ($this->hookUrl->getHost() === parse_url($definition['config']['url'], PHP_URL_HOST)) {
                return $definition;
            }
        }
        return null;
    }

    /**
     * Example:
     *
     *     list($user, $repository = $this->getUserAndRepositoryFrom($url);
     *
     * @param RepositoryUrl $url
     * @return array<string|null>
     */
    private function getUserAndRepositoryFrom(RepositoryUrl $url)
    {
        if ($url->getHost() !== 'github.com') {
            return array(null, null);
        }
        $segments = $url->getPathSegments();
        if (count($segments) !== 2) {
            return array(null, null);
        }
        list($owner, $repository) = $segments;
        $repository = basename($repository, '.git');
        return array($owner, $repository);
    }
}
