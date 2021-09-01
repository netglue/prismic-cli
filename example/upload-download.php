<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

/**
 * An example of using the shipped Symfony CLI commands to create a standalone tool for uploading and downloading type definitions
 */

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Primo\Cli\BuildConfig;
use Primo\Cli\Console\DownloadCommand;
use Primo\Cli\Console\UploadCommand;
use Primo\Cli\TypePersister;
use Prismic\DocumentType\BaseClient;
use Symfony\Component\Console\Application;

$repo = getenv('PRISMIC_REPOSITORY');
if (! is_string($repo) || empty($repo)) {
    echo 'Define a Prismic repository in the environment variable `PRISMIC_REPOSITORY`';
    exit(1);
}

$token = getenv('PRISMIC_TYPE_TOKEN');
if (! is_string($token) || empty($token)) {
    echo 'Define a Prismic access token for the types API in the environment variable `PRISMIC_TYPE_TOKEN`';
    exit(1);
}

$client = new BaseClient(
    $token,
    $repo,
    Psr18ClientDiscovery::find(),
    Psr17FactoryDiscovery::findRequestFactory(),
    Psr17FactoryDiscovery::findUriFactory(),
    Psr17FactoryDiscovery::findStreamFactory()
);

/**
 * Define the Source and destination directories.
 *
 * Source contains php source files and the dist directory is the target directory for Json Specs - in this case, our backup
 */
$source = __DIR__ . '/source';
$dist = __DIR__ . '/dist';

/**
 * The same types as used in ./example.php - these are only required for uploading purposes.
 */
$types = [
    [
        'id' => 'page',
        'name' => 'A Web Page',
        'repeatable' => true,
    ],
    [
        'id' => 'error',
        'name' => 'An Error Page',
        'repeatable' => true,
    ],
    [
        'id' => 'article',
        'name' => 'Blog Post',
        'repeatable' => true,
    ],
];

/**
 * Build config will throw exceptions if there are any configuration problems.
 */
$config = BuildConfig::withArraySpecs($source, $dist, []);
$persister = new TypePersister($config, $client);

$application = new Application('Primo Upload and Download Example');
$application->add(new DownloadCommand($persister, $config));
$application->add(new UploadCommand($persister, $config));

return $application->run();
