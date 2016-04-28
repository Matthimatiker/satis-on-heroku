<?php

namespace Matthimatiker\SatisOnHeroku\Tests;

use Matthimatiker\SatisOnHeroku\RepositoryUrl;
use Matthimatiker\SatisOnHeroku\SatisConfig;

class SatisConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * System under test.
     *
     * @var SatisConfig
     */
    private $config = null;

    /**
     * Initializes the test environment.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->config = new SatisConfig(__DIR__ . '/_files/satis.json');
    }

    /**
     * Cleans up the test environment.
     */
    protected function tearDown()
    {
        $this->config = null;
        parent::tearDown();
    }

    public function testGetRepositoryUrlsReturnsCorrectNumberOfUrls()
    {
        $urls = $this->config->getRepositoryUrls();

        $this->assertGreaterThanOrEqual(3, count($urls));
    }

    public function testGetRepositoryUrlsReturnsUrlObjects()
    {
        $urls = $this->config->getRepositoryUrls();

        $this->assertContainsOnly(RepositoryUrl::class, $urls);
    }

    public function testGetRepositoryHostsReturnsCorrectHosts()
    {
        $hosts = $this->config->getRepositoryHosts();

        $this->assertInternalType('array', $hosts);
        $this->assertContains('my-company.kilnhg.com', $hosts);
        $this->assertContains('github.com', $hosts);
    }

    public function testGetRepositoryHostsDoesNotReturnSameHostTwice()
    {
        $hosts = $this->config->getRepositoryHosts();

        $this->assertInternalType('array', $hosts);
        $numberOfEntriesPerHost = array_count_values($hosts);
        $this->assertArrayHasKey('github.com', $numberOfEntriesPerHost);
        $this->assertEquals(1, $numberOfEntriesPerHost['github.com']);
    }

    public function testGetGitHubTokenReturnsCorrectValue()
    {
        $this->assertEquals('MySecretGitHubToken', $this->config->getGitHubToken());
    }

    public function testThrowsExceptionIfConfigFileDoesNotExist()
    {
        $this->setExpectedException(\Exception::class);
        new SatisConfig(__DIR__ . '/_files/missing.json');
    }
}
