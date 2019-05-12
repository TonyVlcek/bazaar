<?php declare(strict_types=1);


namespace Wolfpup\Scraper\Resources;


use Wolfpup\Scraper\Model\Item;

interface IResource
{

    public function getName(): string;

    public function getBaseUrl(): string;

    // Processing List Page
    public function getDetailUrls(string $listPageBody): array;

    public function getNextListsUrls(string $listPageBody): array;

    // Processing Detail Page
    public function parseDetailPage(string $pageBody): Item;

}
