<?php declare(strict_types=1);

namespace Wolfpup\Scraper\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wolfpup\Scraper\Resources\SBazarResource;
use Wolfpup\Scraper\Scraper;

class ScrapeCommand extends Command
{

    protected static $defaultName = 'scrape';


    protected function configure()
    {
        $this->setDescription('Some description')
            ->setHelp('Some Help');

        $this->addArgument('resource', InputArgument::OPTIONAL, 'resource');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $resource = $input->getArgument('resource') ?? getenv('RESOURCE');

        if (!$resource) {
            $output->writeln('<error>Resource not specified</error>');
            return 1;
        }

        $output->writeln("Scraping {$resource}...");

        // SBazarResource
        $resource = new SBazarResource();
        $scraper = new Scraper($resource);

        $scraper->run();

        return 0;
    }

}
