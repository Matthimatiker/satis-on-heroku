<?php

namespace Matthimatiker\SatisOnHeroku\Tests;

use Matthimatiker\SatisOnHeroku\RepositoryUrl;

class RepositoryUrlTest extends \PHPUnit_Framework_TestCase
{
    const URL_GITHUB_SSH  = 'git@github.com:Matthimatiker/satis-on-heroku.git';
    const URL_GITHUB_HTTP = 'https://github.com/Matthimatiker/satis-on-heroku.git';
    const URL_KILN_SSH    = 'ssh://user@my-company.kilnhg.com/project/group/repo';
    const URL_KILN_HTTP   = 'https://my-company.kilnhg.com/Code/project/group/repo.git';
    const URL_PACKAGIST   = 'https?://packagist.org';

    public function testToStringReturnsOriginalGitHubSshUrl()
    {
        $this->assertEquals(self::URL_GITHUB_SSH, (string)$this->createFrom(self::URL_GITHUB_SSH));
    }

    public function testToStringReturnsOriginalGitHubHttpUrl()
    {
        $this->assertEquals(self::URL_GITHUB_HTTP, (string)$this->createFrom(self::URL_GITHUB_HTTP));
    }

    public function testToStringReturnsOriginalKilnSshUrl()
    {
        $this->assertEquals(self::URL_KILN_SSH, (string)$this->createFrom(self::URL_KILN_SSH));
    }

    public function testToStringReturnsOriginalKilnHttpUrl()
    {
        $this->assertEquals(self::URL_KILN_HTTP, (string)$this->createFrom(self::URL_KILN_HTTP));
    }

    public function testToStringReturnsOriginalPackagistUrl()
    {
        $this->assertEquals(self::URL_PACKAGIST, (string)$this->createFrom(self::URL_PACKAGIST));
    }

    public function testGetHostReturnsCorrectValueForGitHubSshUrl()
    {
        $this->assertEquals('github.com', $this->createFrom(self::URL_GITHUB_SSH)->getHost());
    }

    public function testGetHostReturnsCorrectValueForGitHubHttpUrl()
    {
        $this->assertEquals('github.com', $this->createFrom(self::URL_GITHUB_HTTP)->getHost());
    }

    public function testGetHostReturnsCorrectValueForKilnSshUrl()
    {
        $this->assertEquals('my-company.kilnhg.com', $this->createFrom(self::URL_KILN_SSH)->getHost());
    }

    public function testGetHostReturnsCorrectValueForKilnHttpUrl()
    {
        $this->assertEquals('my-company.kilnhg.com', $this->createFrom(self::URL_KILN_HTTP)->getHost());
    }

    public function testGetHostReturnsCorrectValueForPackagistUrl()
    {
        $this->assertEquals('packagist.org', $this->createFrom(self::URL_PACKAGIST)->getHost());
    }

    /**
     * @param string $url
     * @return RepositoryUrl
     */
    private function createFrom($url)
    {
        return new RepositoryUrl($url);
    }
}
