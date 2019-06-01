<?php declare(strict_types=1);


namespace Wolfpup\Scraper;


use React\EventLoop\LoopInterface;
use React\MySQL\ConnectionInterface;
use React\MySQL\Factory;
use Wolfpup\Scraper\Model\Item;

class Storage
{

    /** @var ConnectionInterface */
    private $connection;


    public function __construct(LoopInterface $loop, string $uri)
    {
        $this->connection = (new Factory($loop))->createLazyConnection($uri);
    }

    public function save(Item $item)
    {
        $query = "INSERT INTO items
                      (`resource`, `detail_url`, `title`, `description`)
                      VALUES (?, ?, ?, ?)";
        $this->connection->query(
            $query,
            [$item->resource, $item->url, $item->title, $item->description]
        )->then(
            function () use ($item) {
                echo "✅ Item {$item->title} saved" . PHP_EOL;
            },
            function (\Exception $e) {
                echo "⚠ Error: {$e->getMessage()}" . PHP_EOL;
            }
        );
    }

    public function saveAll(Item ...$items)
    {
        foreach ($items as $item) {
            $this->save($item);
        }
    }

}
