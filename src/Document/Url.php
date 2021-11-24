<?php
namespace App\Document;

use DateTime;
use Exception;
use OpenApi\Annotations as OA;
use App\Repository\UrlRepository;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Url
 * @package App\Document
 * @author denysaw
 *
 * @ODM\Document(repositoryClass=UrlRepository::class)
 * @ODM\HasLifecycleCallbacks
 * @OA\AdditionalProperties(
 *     @OA\Property(property="short_url", type="string", description="Short URL with protocol and domain")
 * )
 */
class Url
{

	/**
	 * @ODM\Id(strategy="auto")
	 * @OA\Property(property="slug", description="Unique url slug")
	 */
	private $id;

	/**
	 * @ODM\Field(type="string")
	 * @ODM\UniqueIndex
	 * @Assert\Url
	 * @OA\Property(property="path", description="Full initial url")
	 */
	private $path;

	/**
	 * @ODM\Field(type="int")
	 * @OA\Property(property="count", description="Number of redirections")
	 */
	private $count;

	/**
	 * @ODM\Field(type="date")
	 * @OA\Property(property="createdAt", description="Date of the first successful storing")
	 */
	private $createdAt;


	/**
	 * @return string
	 */
	public function getId(): string
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getPath(): string
	{
		return $this->path;
	}

	/**
	 * @param string $path
	 */
	public function setPath(string $path): void
	{
		$this->path = $path;
	}

	/**
	 * @return int
	 */
	public function getCount(): int
	{
		return (int) $this->count;
	}

	public function increaseCount(): void
	{
		$this->count++;
	}

	/**
	 * @return DateTime
	 * @throws Exception
	 */
	public function getCreatedAt(): DateTime
	{
		if (is_string($this->createdAt)) {
			$this->createdAt = new DateTime($this->createdAt);
		}

		return $this->createdAt;
	}

	/** @ODM\PrePersist */
	public function prePersist(): void
	{
		// Second url validation layer
		if (!filter_var($this->path, FILTER_VALIDATE_URL)) {
			throw new BadRequestException("Request should contain a valid url");
		}

		$this->createdAt = date('Y-m-d H:i:s');
		$this->count = $this->count ?? 0;
	}

	/**
	 * @param string $baseUrl
	 * @return array
	 * @throws Exception
	 */
	public function serialize(string $baseUrl): array
	{
		return [
			'path'      => $this->getPath(),
			'slug'      => $this->getId(),
			'short_url' => $baseUrl. $this->getId(),
			'createdAt' => $this->getCreatedAt()->format('Y-m-d H:i:s')
		];
	}

	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return $this->path;
	}
}
