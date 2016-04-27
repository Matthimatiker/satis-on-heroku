<?php

namespace Matthimatiker\SatisOnHeroku\Tests;

class RepositoryUrlTest extends \PHPUnit_Framework_TestCase
{
    const URL_GITHUB_SSH  = 'git@github.com:Matthimatiker/satis-on-heroku.git';
    const URL_GITHUB_HTTP = 'https://github.com/Matthimatiker/satis-on-heroku.git';
    const URL_KILN_SSH    = 'ssh://user@my-company.kilnhg.com/project/group/repo';
    const URL_KILN_HTTP   = 'https://my-company.kilnhg.com/Code/project/group/repo.git';
    const URL_PACKAGIST   = 'https?://packagist.org';

    public function testToStringReturnsOriginalGitHubSshUrl()
    {

    }

    public function testToStringReturnsOriginalGitHubHttpUrl()
    {

    }

    public function testToStringReturnsOriginalKilnSshUrl()
    {

    }

    public function testToStringReturnsOriginalKilnHttpUrl()
    {

    }

    public function testGetHostReturnsCorrectValueForGitHubSshUrl()
    {

    }

    public function testGetHostReturnsCorrectValueForGitHubHttpUrl()
    {

    }

    public function testGetHostReturnsCorrectValueForKilnSshUrl()
    {

    }

    public function testGetHostReturnsCorrectValueForKilnHttpUrl()
    {

    }
}
