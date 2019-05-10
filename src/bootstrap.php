<?php

use Wolfpup\Scraper\Commands\ScrapeCommand;

require __DIR__ . '/../vendor/autoload.php';

$application = new \Symfony\Component\Console\Application('scraper', '0.0.1');

$application->add(new ScrapeCommand());

return $application;
