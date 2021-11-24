<?php
declare(strict_types=1);

namespace App\Controller;

use App\Document\Url;
use OpenApi\Annotations as OA;
use Doctrine\ODM\MongoDB\LockException;
use Doctrine\ODM\MongoDB\MongoDBException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ODM\MongoDB\Mapping\MappingException;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class FrontController
 * @package App\Controller
 * @author denysaw
 * @OA\Tag(name="Public endpoints")
 */
class FrontController extends Controller
{

	/**
	 * Redirects to a stored full url
	 *
	 * @Route("/{slug}", methods={"GET"})
	 *
	 * @OA\Parameter(
	 *     name="slug",
	 *     in="path",
	 *     description="Slug of shortened url",
	 *     @OA\Schema(type="string")
	 * )
	 *
	 * @OA\Response(
	 *     response=302,
	 *     description="Redirect to a pre-saved url",
	 * )
	 *
	 * @param string $slug
	 * @return RedirectResponse|JsonResponse
	 * @throws LockException
	 * @throws MappingException
	 * @throws MongoDBException
	 */
	public function findAndRedirect(string $slug): Response
	{
		/** @var Url $url */
		$url = $this->urlRepository->find($slug);

		if (!$url) {
			return new JsonResponse(['error' => "Url with such slug is not found"]);
		}

		$url->increaseCount();
		$this->dm->persist($url);
		$this->dm->flush();

		return new RedirectResponse((string) $url);
	}

	/**
	 * Returns redirection count of a given url
	 *
	 * @Route("/{slug}/count", methods={"GET"})
	 *
	 * @OA\Parameter(
	 *     name="slug",
	 *     in="path",
	 *     description="Slug of shortened url",
	 *     @OA\Schema(type="string")
	 * )
	 *
	 * @OA\Response(
	 *     response=200,
	 *     description="Object with `count` property",
	 *     @OA\JsonContent(
	 *        type="object",
	 *        @OA\Property(
	 *           property="count",
	 *           type="integer"
	 *        )
	 *     )
	 * )
	 *
	 * @param string $slug
	 * @return JsonResponse
	 * @throws LockException
	 * @throws MappingException
	 * @throws MongoDBException
	 */
	public function getCount(string $slug): JsonResponse
	{
		/** @var Url $url */
		$url = $this->urlRepository->find($slug);

		if (!$url) {
			return new JsonResponse(['error' => "Url with such slug is not found"]);
		}

		return new JsonResponse(['count' => $url->getCount()]);
	}
}
