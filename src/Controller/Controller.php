<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\UrlRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class Controller
 * @package App\Controller
 * @author denysaw
 */
class Controller extends AbstractController
{

	/**
	 * @var DocumentManager
	 */
	protected $dm;

	/**
	 * @var UrlRepository
	 */
	protected $urlRepository;


	/**
	 * Controller constructor.
	 * @param DocumentManager $dm
	 * @param UrlRepository $urlRepository
	 * @author denysaw
	 */
	public function __construct(DocumentManager $dm, UrlRepository $urlRepository)
	{
		$this->urlRepository = $urlRepository;
		$this->dm = $dm;
	}
}
