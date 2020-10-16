<?php

declare(strict_types=1);

use Primo\Cli\Console\InfoCommand;
use Primo\Cli\Console\ListCommand;
use Prismic\Api;
use Symfony\Component\Console\Application;

require __DIR__ . '/../vendor/autoload.php';

$api = Api::get('https://primo.prismic.io/api/v2');

$application = new Application('Repository Info Commands Example');
$application->add(new InfoCommand($api));
$application->add(new ListCommand($api));

return $application->run();
