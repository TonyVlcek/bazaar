<?php declare(strict_types=1);

namespace Wolfpup\Scraper\Model;

use DateTimeImmutable;

class Item
{

    /** @var int*/
    public $id;

    /** @var string */
    public $resource;

    /** @var string */
    public $url;

    /** @var string|null */
    public $titlePhotoUrl;

    /** @var string */
    public $title;

    /** @var string|null */
    public $description;

    /** @var DateTimeImmutable|null */
    public $publishedDate;

    /** @var string|null */
    public $location;

    /** @var string|null */
    public $price;

    /** @var DateTimeImmutable */
    public $firstScanDate;

    /** @var DateTimeImmutable */
    public $lastScanDate;

}
