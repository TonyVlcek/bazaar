<?php declare(strict_types=1);


namespace Wolfpup\Scraper\Resources;


class SBazarResource implements IResource
{

    public function getBaseUrl(): string
    {
        return 'https://www.sbazar.cz';
    }

    public function fetchCategories(): array
    {
        return [];
    }
}
