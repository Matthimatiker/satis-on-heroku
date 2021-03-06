<?php

namespace Matthimatiker\SatisOnHeroku;

use Composer\Config;
use Composer\Json\JsonFile;

/**
 * Provides access to the Satis configuration.
 */
class SatisConfig
{
    /**
     * @var Config
     */
    protected $config = null;

    /**
     * @param string|null $configFile Path to the Satis config file.
     */
    public function __construct($configFile = null)
    {
        if ($configFile === null) {
            $configFile = __DIR__ . '/../satis.json';
        }
        $satisConfigFile = new JsonFile($configFile);
        $this->config = new Config();
        $this->config->merge($satisConfigFile->read());
    }

    /**
     * Returns the URLs of all configured repositories.
     *
     * @return RepositoryUrl[]
     */
    public function getRepositoryUrls()
    {
        $configuredRepositories = array_map(function (array $repositoryData) {
            if (!isset($repositoryData['url'])) {
                return null;
            }
            return new RepositoryUrl($repositoryData['url']);
        }, $this->config->getRepositories());
        return array_filter($configuredRepositories);
    }

    /**
     * Returns the host names of all configured repositories.
     *
     * @return string[]
     */
    public function getRepositoryHosts()
    {
        $hosts = array_map(function (RepositoryUrl $repositoryUrl) {
            return $repositoryUrl->getHost();
        }, $this->getRepositoryUrls());
        return array_unique($hosts);
    }

    /**
     * Returns the GitHub token, if available.
     *
     * @return string|null
     */
    public function getGitHubToken()
    {
        $auth = $this->config->get('github-oauth');
        return (isset($auth['github.com'])) ? $auth['github.com'] : null;
    }
}
