<?php
namespace App\Repository;

use App\Document\Url;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;

/**
 * Class UrlRepository
 * @package App\Repository
 * @author denysaw
 */
class UrlRepository extends ServiceDocumentRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Url::class);
	}
}
