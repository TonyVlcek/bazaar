<?php declare(strict_types=1);

namespace Wolfpup\Scraper;

use Clue\React\Mq\Queue;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop;
use Symfony\Component\Console\Output\OutputInterface;
use Wolfpup\Scraper\Resources\IResource;
use Wolfpup\Scraper\Resources\ResourceProvider;


class Scraper
{

    /** @var mixed[] */
    private $options;

    /** @var IResource */
    private $resource;

    /** @var string[] */
    private $rootPages;

    /** @var OutputInterface */
    private $output;

    /** @var Queue */
    private $requestQueue;

    /** @var Storage */
    private $storage;


    public function __construct(array $options, ResourceProvider $resourceProvider)
    {
        $this->options = $options;
        $this->resource = $resourceProvider->getResource();
    }

    /**
     * @param OutputInterface $output
     * @param string[] $rootPages
     */
    public function run(OutputInterface $output, array $rootPages)
    {
        $this->output = $output;
        $this->rootPages = $rootPages;

        $loop = EventLoop\Factory::create();

        $this->requestQueue = RequestQueueFactory::create($this->options['concurrency'], $loop);
        $this->storage = new Storage($loop, $this->options['mysqlUri']);

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

            //TODO: Check whether scan is even needed (url in db, max age, etc...)

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
        $this->output->writeln("♻ Processing LIST: {$url}");

        $this->pushToDetailsQueue(...$this->resource->getDetailUrls($responseBody));
        $this->pushToListsQueue(...$this->resource->getNextListsUrls($responseBody));
    }

    public function processDetailPage(string $responseBody, string $url)
    {
        $this->output->writeln("♻ Processing DETAIL: {$url}");

        // TODO: check where it needs to be parsed -> ideally do this before even sending the request

        $item = $this->resource->parseDetailPage($responseBody);
        //Add meta information
        $item->resource = $this->resource->getName();
        $item->url = $url;
        //TODO: add dates, etc...

        $this->storage->save($item);
    }

    private function printError(\Exception $e)
    {
        $this->output->writeln("<error>" . $e . "</error>");
        exit(1); //TODO throw error exception and let Symfony/Console deal with it
    }

}
