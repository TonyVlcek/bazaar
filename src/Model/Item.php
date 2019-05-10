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

    public function setId(int $id): Item
    {
        $this->id = $id;
        return $this;
    }

    public function setResource(string $resource): Item
    {
        $this->resource = $resource;
        return $this;
    }

    public function setUrl(string $url): Item
    {
        $this->url = $url;
        return $this;
    }

    public function setTitlePhotoUrl(?string $titlePhotoUrl): Item
    {
        $this->titlePhotoUrl = $titlePhotoUrl;
        return $this;
    }

    public function setTitle(string $title): Item
    {
        $this->title = $title;
        return $this;
    }

    public function setDescription(?string $description): Item
    {
        $this->description = $description;
        return $this;
    }

    public function setPublishedDate(?DateTimeImmutable $publishedDate): Item
    {
        $this->publishedDate = $publishedDate;
        return $this;
    }

    public function setLocation(?string $location): Item
    {
        $this->location = $location;
        return $this;
    }

    public function setPrice(?string $price): Item
    {
        $this->price = $price;
        return $this;
    }

    public function setFirstScanDate(DateTimeImmutable $firstScanDate): Item
    {
        $this->firstScanDate = $firstScanDate;
        return $this;
    }

    public function setLastScanDate(DateTimeImmutable $lastScanDate): Item
    {
        $this->lastScanDate = $lastScanDate;
        return $this;
    }

}
