<?php
declare(strict_types=1);

namespace App\Tests\Web;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use function json_decode;
use function json_encode;

class FrontControllerTest extends WebTestCase
{

	/**
	 * @var KernelBrowser
	 */
	private $client;


	public function setUp(): void
	{
		parent::setUp();
		$this->client = static::createClient();
	}

	public function testRedirectAndCount(): void
	{
		// Find by providing the same path first
		$this->client->request('POST', '/api/url', [], [], [], json_encode(['url' => 'https://google.com/']));
		$res = json_decode($this->client->getResponse()->getContent());

		$shortUrl = $res->short_url;
		$slug = $res->slug;

		$this->client->request('GET', $shortUrl);
		$this->assertResponseRedirects('https://google.com/');

		$this->client->request('GET', "/$slug/count");
		$res = json_decode($this->client->getResponse()->getContent());

		$this->assertObjectHasAttribute('count', $res);
		$this->assertEquals(1, $res->count);

		$this->client->request('GET', $shortUrl);
		$this->assertResponseRedirects('https://google.com/');

		$this->client->request('GET', "/$slug/count");
		$res = json_decode($this->client->getResponse()->getContent());

		$this->assertObjectHasAttribute('count', $res);
		$this->assertEquals(2, $res->count);
	}
}
