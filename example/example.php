<?php

declare(strict_types=1);

use Primo\Cli\BuildConfig;
use Primo\Cli\Console\BuildCommand;
use Symfony\Component\Console\Application;

require __DIR__ . '/../vendor/autoload.php';

/**
 * This example will transform the source files in ./source into JSON files in ./dist
 *
 * Examine the source files to get an idea of usage. The PHP files are just regular PHP so you can do anything you like,
 * but they should return an array that is suitable for JSON encoding.
 *
 * There's no requirement to use the 'Type Builder' - it's just a simple collection of static methods to chuck out arrays
 * that fit with the spec for making Prismic document definitions.
 *
 * To build the JSON files, you should run this script in your terminal (After running composer install) like this:
 * > php ./example/example.php primo:build
 *
 * Once you've done that, you can inspect the JSON files in ./dist
 *
 * As this is meant to be integrated into a project, you'll probably have npm-watch watching stuff like your JS and styles;
 * Once configured, you can setup a tool like npm-watch to watch your sources and trigger this tool when something changes.
 *
 * Something like this in your package.json should cover it:
 * "watch": {
 *   "types": {
 *     "patterns": ["example/source/**\/*"],
 *     "extensions": ["php"]
 *   }
 * },
 * "scripts": {
 *   "types": "php path/to/cli-tool primo:build"
 * }
 **/

/**
 * Define an array of Prismic Types that we want to process.
 *
 * Each type is built from a php source file using the id as the filename, i.e. "page.php"
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
 * Define the Source and destination directories.
 *
 * Source contains php source files and the dist directory is the target directory for Json
 */
$source = __DIR__ . '/source';
$dist = __DIR__ . '/dist';

/**
 * Build config will throw exceptions if there are any configuration problems.
 */
$config = BuildConfig::withArraySpecs($source, $dist, $types);

$application = new Application('Primo Builder Example');
$application->add(new BuildCommand($config));

return $application->run();
