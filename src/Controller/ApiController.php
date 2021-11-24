<?php
declare(strict_types=1);

namespace App\Controller;

use App\Document\Url;
use OpenApi\Annotations as OA;
use Doctrine\ODM\MongoDB\LockException;
use Doctrine\ODM\MongoDB\MongoDBException;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ODM\MongoDB\Mapping\MappingException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * Class ApiController
 * @package App\Controller
 * @Route("/api/url")
 * @author denysaw
 * @OA\Tag(name="API endpoints")
 */
class ApiController extends Controller
{

	/**
	 * Retrieves all stored urls
	 *
	 * @Route(methods={"GET"})
	 *
	 * @OA\Response(
	 *     response=200,
	 *     description="JSON array of all existing Url's",
	 *     @OA\JsonContent(
	 *        type="array",
	 *        @OA\Items(ref=@Model(type=Url::class))
	 *     )
	 * )
	 *
	 * @return JsonResponse
	 */
	public function index(): JsonResponse
	{
		$urls = array_map(function($url) {
			return $url->serialize($this->getParameter('baseUrl'));
		}, $this->urlRepository->findAll());

		return new JsonResponse($urls);
	}

	/**
	 * Gets a single url item by slug(id)
	 *
	 * @OA\Parameter(
	 *     name="slug",
	 *     in="path",
	 *     description="Slug of shortened URL",
	 *     @OA\Schema(type="string")
	 * )
	 *
	 * @OA\Response(
	 *     response=200,
	 *     description="Returns object of a found Url record",
	 *     @OA\JsonContent(ref=@Model(type=Url::class))
	 * )
	 *
	 * @Route("/{slug}", methods={"GET"})
	 * @param string $slug
	 * @return JsonResponse
	 * @throws LockException
	 * @throws MappingException
	 */
	public function getOne(string $slug): JsonResponse
	{
		/** @var Url $url */
		$url = $this->urlRepository->find($slug);

		if (!$url) {
			throw new NotFoundHttpException("Url with such slug is not found");
		}

		$baseUrl = $this->getParameter('baseUrl');

		return new JsonResponse($url->serialize($baseUrl));
	}

	/**
	 * Adds new url
	 *
	 * @Route(methods={"POST"})
	 *
	 * @OA\RequestBody(
	 *    @OA\MediaType(
	 *        mediaType="application/json",
	 *        @OA\Schema(
	 *            @OA\Property(
	 *                property="url",
	 *                type="string"
	 *            ),
	 *            example={"url": "https://google.com/"}
	 *        )
	 *    )
	 * ),
	 *
	 * @OA\Response(
	 *     response=200,
	 *     description="Returns object of a newly added Url record",
	 *     @OA\JsonContent(ref=@Model(type=Url::class))
	 * )
	 *
	 * @param Request $request
	 * @return JsonResponse
	 * @throws MongoDBException
	 */
	public function create(Request $request): JsonResponse
	{
		$body = json_decode($request->getContent());

		if (!@$body->url) {
			throw new BadRequestException("Parameter `url` is required");
		}

		// Check if short url for the path is already stored
		$url = $this->urlRepository->findOneBy(['path' => $body->url]);

		if (!$url) {
			$url = new Url();
			$url->setPath($body->url);

			$this->dm->persist($url);
			$this->dm->flush();
		}

		return new JsonResponse($url->serialize($this->getParameter('baseUrl')));
	}

	/**
	 * Removes a single Url item by slug(id)
	 *
	 * @Route("/{slug}", methods={"DELETE"})
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
	 *     description="Success if url was found and deleted",
	 *     @OA\JsonContent(
	 *        type="object",
	 *        @OA\Property(
	 *           property="success",
	 *           type="boolean"
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
	public function delete(string $slug): JsonResponse
	{
		/** @var Url $url */
		$url = $this->urlRepository->find($slug);

		if (!$url) {
			throw new NotFoundHttpException("Url with such slug not found");
		}

		$this->dm->remove($url);
		$this->dm->flush();

		return new JsonResponse(['success' => true]);
	}
}
