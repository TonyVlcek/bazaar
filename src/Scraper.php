<?php declare(strict_types=1);

namespace Wolfpup\Scraper;

use Clue\React\Buzz\Browser;
use Clue\React\Mq\Queue;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop;
use Symfony\Component\DomCrawler\Crawler;
use Wolfpup\Scraper\Model\Item;
use Wolfpup\Scraper\Resources\IResource;

class Scraper
{

    /** @var IResource */
    private $resource;

    private $categories = [];
    private $items = [];
    private $requests = 0;

    /**
     * Scraper constructor.
     * @param IResource $resource
     */
    public function __construct(IResource $resource)
    {
        $this->resource = $resource;
    }


    public function run($concurrency = 10)
    {
        $loop = EventLoop\Factory::create();

        $browser = new Browser($loop);

        //$this->fetchCategories($browser, $this->resource->getBaseUrl());

        $urls = [
            "https://www.sbazar.cz/210-bourana-auta/cela-cr/cena-neomezena/nejnovejsi/1",
            "https://www.sbazar.cz/210-bourana-auta/cela-cr/cena-neomezena/nejnovejsi/2",
        ];

        $queue = new Queue($concurrency, null, function (string $url) use ($browser) {
            return $browser->get($url);
        });

        foreach ($urls as $url) {
            $queue($url)->then(function (ResponseInterface $response) {
                array_push($this->items, ...$this->parseItems((string) $response->getBody()));
            });
        }

        $loop->run();

        print_r($this->items);
    }

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

    public function parseCategories(string $responseBody)
    {
        $crawler = new Crawler($responseBody);

        try {
            return $crawler->filter('a.c-categories__name')->extract(['href', '_text']);
        } catch (\RuntimeException $e) {
            return [];
        }
    }

    /**
     * @param string $responseBody
     * @return Item[]
     * @throws \RuntimeException
     */
    public function parseItems(string $responseBody)
    {
        $crawler = new Crawler($responseBody);
        /** @var Item[] $items */
        $items = [];


        $itemsCrawler = $crawler->filter('.c-item .c-item__group');

        $itemsCrawler->each(
            function (Crawler $crawler) use (&$items) {
                $link = $crawler->filter('.c-item__link')->attr('href');
                $title = $crawler->filter('.c-item__name-text')->text();
                $imgUrl = $crawler->filter('.c-item__image img')->attr('src');

                $attrs = $crawler->filter('.c-item__attrs')->eq(1);
                $price = $attrs->filter('.c-price .c-price__price')->text();
                $location = $attrs->filter('.c-item__attrs')->text();

                if (empty($link) || empty($title)) {
                    throw new \RuntimeException("Some mandatory attributes not parsed properly:
                        link: '{$link}', title : '{$title}'',");
                }

                $item = new Item();
                $item->setUrl($link)
                        ->setTitle($title)
                        ->setTitlePhotoUrl($imgUrl)
                        ->setPrice($price)
                        ->setLocation($location);

                $items[] = $item;
            }
        );

        return $items;
    }
}
