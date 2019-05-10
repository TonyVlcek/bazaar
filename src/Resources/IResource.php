<?php declare(strict_types=1);


namespace Wolfpup\Scraper\Resources;


interface IResource
{

    public function getBaseUrl(): string;

    public function fetchCategories(): array;

}
