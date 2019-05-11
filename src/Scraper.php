<?php declare(strict_types=1);

namespace Wolfpup\Scraper;

use Clue\React\Buzz\Browser;
use Clue\React\Mq\Queue;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;
use Wolfpup\Scraper\Model\Item;
use Wolfpup\Scraper\Resources\IResource;


class Scraper
{

    //TODO: load from config
    private $config = [
        'mysql_uri' => 'root:password@mysql:3306/bazaar',
        'max_age' => '24h', //how old does the entry need to be for it to get rescanned
        'concurrency' => 10,

    ];


    /** @var IResource */
    private $resource;

    /** @var string[] */
    private $rootPages;

    /** @var OutputInterface */
    private $out;

    /** @var Queue */
    private $requestQueue;

    /** @var Storage */
    private $storage;


    public function __construct(IResource $resource, array $rootPages, OutputInterface $out)
    {
        $this->resource = $resource;
        $this->rootPages = $rootPages;
        $this->out = $out;
    }

    public function run()
    {
        $loop = EventLoop\Factory::create();

        $this->requestQueue = RequestQueueFactory::create($this->config['concurrency'], $loop);
        $this->storage = new Storage($loop, $this->config['mysql_uri']);

        $this->pushToListsQueue(...$this->rootPages);

        $loop->run();

    }

    //-----------------------------------------------------------------------------------

    private function pushToListsQueue(string ...$listsUrls)
    {
        foreach ($listsUrls as $url) {
            $q = $this->requestQueue;
            $q($url)->then(
                function (ResponseInterface $response) use ($url) {
                    $this->processListPage((string) $response->getBody(), $url);
                },
                function (\Exception $e) {
                    $this->printError($e);
                }
            );
        }
    }

    private function pushToDetailsQueue(string ...$detailsUrls)
    {
        foreach ($detailsUrls as $url) {
            $q = $this->requestQueue;
            $q($url)->then(
                function (ResponseInterface $response) use ($url) {
                    $this->processDetailPage((string) $response->getBody(), $url);
                },
                function (\Exception $e) {
                    $this->printError($e);
                }
            );
        }
    }

    //-----------------------------------------------------------------------------------

    public function processListPage(string $responseBody, string $url)
    {
        $this->out->writeln("♻ Processing LIST: {$url}");

        $this->pushToDetailsQueue(...$this->resource->getDetailUrls($responseBody));
        $this->pushToListsQueue(...$this->resource->getNextListsUrls($responseBody));
    }

    public function processDetailPage(string $responseBody, string $url)
    {
        $this->out->writeln("♻ Processing DETAIL: {$url}");

        // TODO: check storage -> promise

        $item = $this->resource->parseDetailPage($responseBody);
        //Add meta information
        $item->resource = $this->resource->getName();
        $item->url = $url;
        //TODO: add dates, etc...

        $this->storage->save($item);
    }

    private function printError(\Exception $e)
    {
        $this->out->writeln("<error>" . $e . "</error>");
        exit(1);
    }

}
