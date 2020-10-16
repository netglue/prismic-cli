# PHP Based Prismic CLI Tooling _(WIP)_

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
