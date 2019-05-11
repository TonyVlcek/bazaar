<?php declare(strict_types=1);

namespace Wolfpup\Scraper\Commands;

use Nextras\Dbal\Connection;
use Nextras\Dbal\QueryException;
use Nextras\Dbal\Result\Result;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wolfpup\Scraper\Resources\IResource;
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
class ScrapeCommand extends Command
{

    protected static $defaultName = 'scrape';

    /** @var string */
    private $resource;

    /** @var Connection */
    private $connection;

    /** @var OutputInterface */
    private $output;


    protected function configure()
    {
        $this->setDescription('Some description')
            ->setHelp('Some Help');

        $this->addArgument('resource', InputArgument::OPTIONAL, 'resource');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->resource = $input->getArgument('resource') ?? getenv('RESOURCE');

        if (!$this->resource) {
            $output->writeln('<error>Resource not specified</error>');
            return 1;
        }

        $this->setupConnection();


        $output->writeln("Resource: {$this->resource}");

        $rootPages = $this->getRootPages();

        $output->writeln("Loaded " . count($rootPages) . " root pages.");

        $resource = $this->getResourceInstanceByName();
        $scraper = new Scraper($resource, $rootPages, $output);
        $scraper->run();

        return 0;
    }

    /**
     * @return string[]
     */
    private function getRootPages(): array
    {
        try {
            $result = $this->connection->query("SELECT * FROM [root_pages] WHERE [resource] = %s", $this->resource);
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

    private function getResourceInstanceByName(): IResource
    {
        $namespace = 'Wolfpup\\Scraper\\Resources\\';
        $className = $namespace . str_replace('-', '', ucwords($this->resource, '-')) . 'Resource';

        return new $className();
    }

    private function setupConnection(): void
    {
        //TODO: get from env
        //TODO: Wrap this into some class
        $this->connection = new Connection([
            'driver'   => 'mysqli',
            'host'     => 'mysql',
            'username' => 'root',
            'password' => 'password',
            'database' => 'bazaar',
        ]);
    }

}
