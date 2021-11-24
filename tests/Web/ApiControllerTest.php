<?php
declare(strict_types=1);

namespace App\Tests\Web;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiControllerTest extends WebTestCase
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

	public function testRequestWithoutUrl(): void
	{
		$this->client->request('POST', '/api/url');
		$res = $this->client->getResponse();

		$this->assertResponseStatusCodeSame(400);
		$this->assertStringContainsString("Parameter `url` is required", (string) $res);
	}

	public function testAddInvalidUrl(): void
	{
		$this->client->request('POST', '/api/url', [], [], [], json_encode(['url' => 'simply_text']));
		$res = $this->client->getResponse();

		$this->assertResponseStatusCodeSame(400);
		$this->assertStringContainsString("Request should contain a valid url", (string) $res);
	}

	public function testAddValidUrl(): void
	{
		$this->client->request('POST', '/api/url', [], [], [], json_encode(['url' => 'https://google.com/']));

		$this->assertResponseIsSuccessful();
	}

	public function testRead(): void
	{
		// Find by providing the same path first
		$this->client->request('POST', '/api/url', [], [], [], json_encode(['url' => 'https://google.com/']));
		$res = json_decode($this->client->getResponse()->getContent());
		$slug = $res->slug;

		$this->client->request('GET', "/api/url/$slug");
		$res = json_decode($this->client->getResponse()->getContent());

		$this->assertResponseIsSuccessful();
		$this->assertObjectHasAttribute('short_url', $res);
	}

	public function testDelete(): void
	{
		// Find by providing the same path first
		$this->client->request('POST', '/api/url', [], [], [], json_encode(['url' => 'https://google.com/']));
		$res = json_decode($this->client->getResponse()->getContent());
		$slug = $res->slug;

		$this->client->request('DELETE', "/api/url/$slug");
		$res = json_decode($this->client->getResponse()->getContent());

		$this->assertResponseIsSuccessful();
		$this->assertTrue($res->success);
	}
}
