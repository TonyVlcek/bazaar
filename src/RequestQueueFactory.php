<?php declare(strict_types=1);


namespace Wolfpup\Scraper;


use Clue\React\Buzz\Browser;
use Clue\React\Mq\Queue;
use React\EventLoop\LoopInterface;

/**
 * Class RequestQueueFactory
 * @package Wolfpup\Scraper
 *
 * Encapsulates and abstracts away the instance of browser, while also managing the number of
 * concurrent requests to be sent out.
 */
class RequestQueueFactory
{

    public static function create(int $concurrency, LoopInterface $loop)
    {
        $browser = new Browser($loop);

        return new Queue($concurrency, null, function (string $url) use (&$browser) {
            return $browser->get($url);
        });
    }

}
