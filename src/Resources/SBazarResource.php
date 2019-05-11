<?php declare(strict_types=1);


namespace Wolfpup\Scraper\Resources;


use Symfony\Component\DomCrawler\Crawler;
use Wolfpup\Scraper\Model\Item;

class SBazarResource implements IResource
{

    public function getName(): string
    {
        return 's-bazar';
    }

    public function getBaseUrl(): string
    {
        return 'https://www.sbazar.cz';
    }

    /**
     * @param string $listPageBody
     * @return array
     * @throws \RuntimeException
     */
    public function getDetailUrls(string $listPageBody): array
    {
        $crawler = new Crawler($listPageBody);

        $urls = $crawler->filter('.c-item__link')->extract(['href']);

        return $urls;
    }

    /**
     * @param string $listPageBody
     * @return array
     * @throws \RuntimeException
     */
    public function getNextListsUrls(string $listPageBody): array
    {
        $crawler = new Crawler($listPageBody);

        // In case of SBazar only the next list page can be discovered
        return [$crawler->filter('.c-items-pg__paging a[data-dot="next"]')->attr('href')];
    }

    /**
     * @param string $responseBody
     * @return Item
     * @throws \RuntimeException
     */
    public function parseDetailPage(string $responseBody): Item
    {
        $crawler = new Crawler($responseBody);

        $item = new Item();
        $item->title = $crawler->filter('h1.p-uw-item__header')->text();
        $item->description = $crawler->filter('.p-uw-item__detail-main .p-uw-item__description')->text();
        $item->price = $crawler->filter('.p-uw-item__detail-main .c-price__price')->text();
        $item->location = $crawler->filter('.p-uw-item__detail-info a.atm-link')->text();
        $item->titlePhotoUrl = $crawler->filter('.ob-c-carousel__item-content img')->attr('src');

        //TODO; Get photos

        return $item;
    }


    /*
    public function fetchCategories(Browser $browser, string $url) {
        $browser->get($url)->then(
            function(ResponseInterface $response) use ($browser) {
                $this->requests++;

                $categories = $this->parseCategories((string) $response->getBody());

                foreach ($categories as $category) {
                    if (!key_exists($category[0], $this->categories)) {
                        $this->categories[$category[0]] = $category[1];
                        echo $this->requests . " | " . count($this->categories) . " | " . $category[1] . "\n";
                        $this->fetchCategories($browser, $category[0]);
                    }
                }
            }
        );
    }
    */

    /*
    public function parseCategories(string $responseBody)
    {
        $crawler = new Crawler($responseBody);

        try {
            return $crawler->filter('a.c-categories__name')->extract(['href', '_text']);
        } catch (\RuntimeException $e) {
            return [];
        }
    }
    */
}
