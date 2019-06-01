<?php declare(strict_types = 1);

namespace App\Controllers;

use Apitte\Core\Annotation\Controller\ControllerPath;
use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\Model\Orm\Orm;

/**
 * @ControllerPath("/items")
 */
final class ItemsController extends BaseV1Controller
{

	/** @var Orm */
	private $orm;


	public function __construct(Orm $orm)
	{
		$this->orm = $orm;
	}

	/**
	 * @Path("/")
	 * @Method("GET")
	 */
	public function index(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$result = [];
		$items = $this->orm->items->findAll();

		foreach ($items as $item) {
			$result[] = $item->toArray();
		}

		return $response->writeJsonBody($result);
	}

}
