<?php declare(strict_types=1);


namespace Wolfpup\Scraper\Resources;


use Symfony\Component\DomCrawler\Crawler;
use Wolfpup\Scraper\Model\Item;

class CykloBazarResource implements IResource
{

    public function getName(): string
    {
        return 'cyklo-bazar';
    }

    public function getBaseUrl(): string
    {
        return 'https://www.cyklobazar.cz';
    }

    /**
     * Links at CykloBazar are relative, thus need to be prefixed with the baseUrl.
     *
     * @param string $listPageBody
     * @return array
     * @throws \RuntimeException
     */
    public function getDetailUrls(string $listPageBody): array
    {
        $crawler = new Crawler($listPageBody);

        $urls = $crawler->filter('a.offer__title')->extract(['href']);

        return preg_filter('/^/', $this->getBaseUrl(), $urls);
    }

    /**
     * @param string $listPageBody
     * @return array
     * @throws \RuntimeException
     */
    public function getNextListsUrls(string $listPageBody): array
    {
        $crawler = new Crawler($listPageBody);

        //TODO: Cyklo Bazar can provide multiple
        //This could be done more efficiently, but a mechanism that will prevent scanning page
        //twice needs to be put in place.
        return [$this->getBaseUrl().$crawler->filter('#snippet-vp-paginator a:last-child')->attr('href')];
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
        $item->title = $crawler->filter('.page-header h1')->text();
        $item->description = $crawler->filter('.detail__desc')->text();
        $item->price = $crawler->filter('.detail__table__price')->text();
        $item->titlePhotoUrl = $this->getBaseUrl().$crawler->filter('.img-responsive.center-block.mb5')->attr('src');

        //TODO; Get photos

        return $item;
    }

}
