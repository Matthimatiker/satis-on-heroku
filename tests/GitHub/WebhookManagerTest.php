<?php

namespace Matthimatiker\SatisOnHeroku\Tests\GitHub;

use Github\Api\Repository\Hooks;
use Guzzle\Http\Url;
use Matthimatiker\SatisOnHeroku\GitHub\WebhookManager;
use Matthimatiker\SatisOnHeroku\RepositoryUrl;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class WebhookManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * System under test.
     *
     * @var WebhookManager
     */
    private $manager = null;

    /**
     * The simulated API.
     *
     * @var ObjectProphecy
     */
    private $apiSpy = null;

    /**
     * Simulated data of already registered webhooks.
     *
     * @var array<array<string=>mixed>>
     */
    private $registeredWebhookData = array();

    /**
     * Initializes the test environment.
     */
    protected function setUp()
    {
        parent::setUp();
        $webhookUrl = Url::factory('https://test:password@my-satis.herokuapp.com/github-webhook.php');
        $this->manager = new WebhookManager($this->initializeApiSpy(), $webhookUrl);
    }

    /**
     * Cleans up the test environment.
     */
    protected function tearDown()
    {
        $this->manager = null;
        $this->apiSpy = null;
        parent::tearDown();
    }

    public function testSupportsReturnsFalseIfNonGitHubUrlIsPassed()
    {
        $this->assertFalse($this->manager->supports(new RepositoryUrl('https://heroku.com/no/repo')));
    }

    public function testSupportsReturnsFalseIfGitHubUrlDoesNotContainRepositoryName()
    {
        $this->assertFalse($this->manager->supports(new RepositoryUrl('https://github.com/')));
    }

    public function testSupportsReturnsTrueIfGitHubRepositoryUrlIsPassed()
    {
        $url = new RepositoryUrl('https://github.com/Matthimatiker/satis-on-heroku.git');
        $this->assertTrue($this->manager->supports($url));
    }

    public function testAddsWebhookIfItDoesNotExistYet()
    {
        $url = new RepositoryUrl('https://github.com/Matthimatiker/satis-on-heroku.git');

        $this->manager->registerFor($url);

        $this->apiSpy->create()->shouldHaveBeenCalled();
    }

    public function testAddsWebhookIfItDoesNotExistYetAndOtherHooksAreRegistered()
    {
        $this->simulateRegisteredWebhook('https://any-other.org/hook');
        $url = new RepositoryUrl('https://github.com/Matthimatiker/satis-on-heroku.git');

        $this->manager->registerFor($url);

        $this->apiSpy->create()->shouldHaveBeenCalled();
    }

    public function testUpdatesWebhookIfCredentialsChanged()
    {
        $this->simulateRegisteredWebhook('https://unit:test@my-satis.herokuapp.com/github-webhook.php');
        $url = new RepositoryUrl('https://github.com/Matthimatiker/satis-on-heroku.git');

        $this->manager->registerFor($url);

        $this->apiSpy->update()->shouldHaveBeenCalled();
    }

    public function testUpdatesWebhookIfUrlPathChanged()
    {
        $this->simulateRegisteredWebhook('https://test:password@my-satis.herokuapp.com/webhook.php/github');
        $url = new RepositoryUrl('https://github.com/Matthimatiker/satis-on-heroku.git');

        $this->manager->registerFor($url);

        $this->apiSpy->update()->shouldHaveBeenCalled();
    }

    public function testDoesNotUpdateIfWebhookDidNotChange()
    {
        $this->simulateRegisteredWebhook('https://test:password@my-satis.herokuapp.com/github-webhook.php');
        $url = new RepositoryUrl('https://github.com/Matthimatiker/satis-on-heroku.git');

        $this->manager->registerFor($url);

        $this->apiSpy->update()->shouldNotHaveBeenCalled();
        $this->apiSpy->create()->shouldNotHaveBeenCalled();
    }

    /**
     * @return Hooks
     */
    private function initializeApiSpy()
    {
        $this->apiSpy = $this->prophesize(Hooks::class);
        $this->apiSpy->all(Argument::any(), Argument::any())->will(function () {
            return $this->registeredWebhookData;
        });
        return $this->apiSpy->reveal();
    }

    /**
     * @param string $url
     */
    private function simulateRegisteredWebhook($url)
    {
        $id = count($this->registeredWebhookData) + 1;
        $this->registeredWebhookData[] = array (
            'type' => 'Repository',
            'id' => $id,
            'name' => 'web',
            'active' => true,
            'events' => array ('push'),
            'config' => array (
                'content_type' => 'json',
                'insecure_ssl' => '0',
                'url' => $url,
            ),
            'updated_at' => '2016-04-26T21:18:25Z',
            'created_at' => '2016-04-24T15:01:05Z',
            'url' => 'https://api.github.com/repos/Matthimatiker/satis-on-heroku/hooks/' . $id,
            'test_url' => 'https://api.github.com/repos/Matthimatiker/satis-on-heroku/hooks/' . $id . '/test',
            'ping_url' => 'https://api.github.com/repos/Matthimatiker/satis-on-heroku/hooks/' . $id . '/pings',
            'last_response' => array (
                'code' => 504,
                'status' => 'timeout',
                'message' => 'Service Timeout'
            )
        );
    }
}
