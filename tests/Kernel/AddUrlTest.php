<?php
declare(strict_types=1);

namespace App\Tests\Kernel;

use App\Document\Url;
use App\Repository\UrlRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

final class AddUrlTest extends KernelTestCase
{

	/**
	 * @var DocumentManager
	 */
	private $dm;

	/**
	 * @var UrlRepository
	 */
	private $urlRepository;


	public function setUp(): void
	{
		parent::setUp();
		self::bootKernel();
		$container = self::getContainer();

		$this->urlRepository = $container->get(UrlRepository::class);
		$this->dm = $container->get(DocumentManager::class);
	}

	public function testAddInvalidUrl(): void
	{
		$url = new Url();
		$url->setPath('simply_text');

		$this->expectException(BadRequestException::class);
		$this->expectExceptionMessage("Request should contain a valid url");
		$this->dm->persist($url);
	}

	/**
	 * @throws MongoDBException
	 */
	public function testAddValidUrl(): void
	{
		$url = new Url();
		$url->setPath('https://google.com/');

		$this->dm->persist($url);
		$this->dm->flush();

		$this->assertIsString($url->getId());
	}

	public function testSearchAndDelete(): void
	{
		/** @var Url $url */
		$url = $this->urlRepository->findOneBy(['path' => 'https://google.com/']);
		$this->assertInstanceOf(Url::class, $url);

		$this->dm->remove($url);
		$this->dm->flush();

		$url = $this->urlRepository->find($url->getId());
		$this->assertNull($url);
	}
}
