# PHP Based Prismic CLI Tooling

[![Build Status](https://github.com/netglue/prismic-cli/workflows/Continuous%20Integration/badge.svg)](https://github.com/netglue/prismic-cli/actions?query=workflow%3A"Continuous+Integration")

[![codecov](https://codecov.io/gh/netglue/prismic-cli/branch/main/graph/badge.svg)](https://codecov.io/gh/netglue/prismic-cli)
[![Psalm Type Coverage](https://shepherd.dev/github/netglue/prismic-cli/coverage.svg)](https://shepherd.dev/github/netglue/prismic-cli)

[![Latest Stable Version](https://poser.pugx.org/netglue/prismic-cli/v/stable)](https://packagist.org/packages/netglue/prismic-cli)
[![Total Downloads](https://poser.pugx.org/netglue/prismic-cli/downloads)](https://packagist.org/packages/netglue/prismic-cli)

This is a development tool based on Symfony Console so require it with `--dev` - it's meant to be used as part of a
build process with a tool like `npm-watch` or similar.

```bash
$ composer require --dev netglue/prismic-cli
```

The primary installation target for this lib is a [Mezzio](https://github.com/mezzio) app as it has been built to
integrate with the [Laminas CLI](https://github.com/laminas/laminas-cli) package.

During installation you should be prompted to inject the config provider(s) if installing as part of a Mezzio app.

`laminas/laminas-cli` is not required by this lib, so you'll need to require it too if that's what you want.

There's nothing to stop you from using this tool 'stand-alone' - the `./example/example.php` should point you in the
right direction for this. It's also worth inspecting that example so everything makes sense.

### Building Document Model JSON Files

The command `primo:build`, given some configuration, generates JSON files from PHP sources that you can copy
and paste into [Prismic.io's](https://prismic.io) custom type editor.

Assuming that you are using a DI container that returns application-wide config as an array using `config` as a service id,
You can drop configuration similar to this in order to have the tool build your types upon invocation.

```php
<?php
return [
    'primo' => [
        'cli' => [
            'builder' => [
                'source' => __DIR__ . '/../directory/where/plain-php-files-are',
                'dist' => __DIR__ . '/../where-you-want-the-json-files-to-go',
            ],
        ],
        'types' => [
            [
                'id' => 'some-type',
                'name' => 'My Document Type',
                'repeatable' => true,
            ],
        ],
    ],
];
```

The lib currently lacks documentation and a decent test suite but there is an annotated example in `./example`. When it comes to 
configuring as part of a Mezzio app, please examine `./src/ConfigProvider.php` for more information.

### Upload, Download and Diff Document Models Against the Remote Repository

If you have setup the "Custom Types API" and have a valid access token to use it, adding the following to your configuration along with the contents of `CustomTypeApiConfigProvider` will configure 3 additional commands that will enable you to upload, download and diff changes between your local and remote definitions:

```php
<?php
// Local Configuration
return [
    'primo' => [

        // ...

        'custom-type-api' => [
            'token' => 'an access token retrieved from repository settings',
            'repository' => 'my-repo', // The repo name such as "my-repo" as opposed to the full url or "my-repo.prismic.io"
        ],

        // ...
    ],
];
```

Once configured, you can issue

- `primo:types:download` to download all JSON definitions to your local dist directory, or add a `type` argument to download just one of them.
- `primo:types:upload` to upload locally defined definitions to the remote types api make them immediately available in your repository. Again, a `type` argument will process a single definition.
- `primo:types:diff` will produce colourised diffs in your console showing the changes between local and remote.

These tools make use of [`netglue/prismic-doctype-client`](https://github.com/netglue/prismic-doctype-client), so check that out if you'd like some more information, also [link to the Prismic Custom Types API Docs](https://prismic.io/docs/technologies/custom-types-api).

### Commands that Query a Repository

Theres also some commands for getting information from a repository. These commands are opt-in. During installation there's a config
provider called `ApiToolsConfigProvider` which you can skip if you don't want these tools available.

All of the commands require a configured Api Client, using [`netglue/prismic-client`](https://github.com/netglue/prismic-client).

The above mentioned config provider sets up its own factory for the api client, skipping a cache implementation as it's likely that
if you are using the tools, you don't want stale information.

Configure the repository somewhere with information similar to:
```php
return [
    'prismic' => [
        'api' => 'https://your-repo.cdn.prismic.io/api/v2',
        'token' => null, // Or 'string-access-token'
    ],
];
```

#### Currently Available Commands

- `primo:info` - Without arguments, provides information about the repository itself
- `primo:info <document-id>` - Shows information about a specific document
- `primo:list` - Lists the types available in the configured repository
- `primo:list <type>` - Lists documents and id's of a specific type

You can try out these commands on the test repo used for the Prismic/Mezzio integration lib we wrote at [`netglue/primo`](https://github.com/netglue/primo) by running `./example/api-queries.php`

_Note: This lib is not a replacement for JS cli tooling provided by Prismic…_
