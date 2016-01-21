<?php

use Pimple\Container;
use Symfony\Component\Yaml\Yaml;

/**
 * Pimple dependency injection configuration
 */
$c = new Container;
$c['test.command'] = function($c) {
    return new Test\Command\ScrapeCommand($c['test.scraper']);
};
$c['test.command_application'] = function($c) {
    $app = new Symfony\Component\Console\Application;
    $app->addCommands([$c['test.command']]);
    return $app;
};
$c['test.scraper'] = function($c) {
    $config = Yaml::parse(\file_get_contents(__DIR__ . '/config/config.yml'));
    return new Test\Scraper\Scraper($c['test.client'], $c['test.entity_collection'], $config);
};
$c['test.client'] = function($c) {
    return new Guzzle\Http\Client;
};
$c['test.entity_collection'] = function($c) {
    return new Test\Collection\EntityCollection;
};
return $c;
