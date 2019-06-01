<?php declare(strict_types=1);

namespace Wolfpup\Scraper\Commands;

use Nextras\Dbal\Connection;
use Nextras\Dbal\QueryException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wolfpup\Scraper\Resources\IResource;
use Wolfpup\Scraper\Resources\ResourceProvider;
use Wolfpup\Scraper\Scraper;

/**
 * Class ScrapeCommand
 * @package Wolfpup\Scraper\Commands
 *
 * # Terms:
 *  - Root Page     : URL where scraping starts
 *  - List Page     : One page with results (list of items)
 *  - Detail Page   : Detail of an item with all details
 *
 * # Life Cycle:
 * 1. Fetch list of Root Pages from db
 * 2. Use Resource to parse all List Pages. Parsing produces:
 *      a) Collection of URLs to Item Detail Pages
 *      b) URL (or collection of URLs) for next List Page
 * 3. Discovered List Pages and detail are added to their respective queues and processed
 */
final class ScrapeCommand extends Command
{

    protected static $defaultName = 'scrape';

    /** @var IResource */
    private $resource;

    /** @var Connection */
    private $connection;

    /** @var Scraper */
    private $scraper;

    /** @var OutputInterface */
    private $output;


    public function __construct(Connection $connection, ResourceProvider $resourceProvider, Scraper $scraper)
    {
        $this->connection = $connection;
        $this->resource = $resourceProvider->getResource();
        $this->scraper = $scraper;

        parent::__construct();
    }


    protected function configure()
    {
        $this->setDescription('Some description')
            ->setHelp('Some Help');

        //$this->addArgument('resource', InputArgument::OPTIONAL, 'resource'); ---> only via env variable
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $output->writeln("Resource: {$this->resource->getName()}");

        $rootPages = $this->getRootPages();
        $output->writeln("Loaded " . count($rootPages) . " root pages.");

        $this->scraper->run($this->output, $rootPages);

        return 0;
    }

    /**
     * @return string[]
     */
    private function getRootPages(): array
    {
        try {
            $result = $this->connection->query(
                "SELECT * FROM [root_pages] WHERE [resource] = %s",
                $this->resource->getName()
            );
            $urls = [];
            foreach ($result as $row) {
                $urls[] = $row->url;
            }

            return $urls;
        } catch (QueryException $e) {
            $this->output->writeln('<error>' . $e . '</error>');
            exit(1);
        }
    }

}
