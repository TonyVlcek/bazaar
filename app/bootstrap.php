<?php

require __DIR__ . '/../vendor/autoload.php';

$configurator = new \Nette\Configurator();

// Define (or load default env vars)

$configurator->setDebugMode(getenv('DEBUG_MODE') === '1');
$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->addConfig(__DIR__ . '/config/config.neon');


return $container = $configurator->createContainer();
