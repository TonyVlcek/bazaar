<?php declare(strict_types=1);


namespace Wolfpup\Scraper\Resources;


use Nette\DI\Container;

class ResourceProvider
{

    /** @var IResource */
    private $resource;

    public function __construct(string $resourceName, Container $container)
    {
        $this->resource = $container->getService($resourceName);
    }

    public function getResource()
    {
        return $this->resource;
    }

}
