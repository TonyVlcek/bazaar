<?php

namespace App\Model\Orm\Items;

use Nextras\Orm\Repository\Repository;


/**
 * @method Item|NULL getById($id)
 */
class ItemsRepository extends Repository
{

	static function getEntityClassNames()
	: array
	{
		return [Item::class];
	}
}
