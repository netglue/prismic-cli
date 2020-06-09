# PHP Based Prismic CLI Tooling _(WIP)_

This is a development tool based on Symfony Console so require it with `--dev` - it's meant to be used as part of a
build process with a tool like `npm-watch` or similar.

```bash
$ composer require --dev netglue/prismic-cli
```

The primary installation target for this lib is a [Mezzio](https://github.com/mezzio) app as it has been built to
integrate with the forthcoming [Laminas CLI](https://github.com/laminas/laminas-cli) package. In lieu of Laminas CLI, it
should also play nicely with my own Symfony console bootstrap package at
[netglue/laminas-symfony-console](https://github.com/netglue/laminas-symfony-console). During installation you should be
prompted to inject the config provider if installing as part of a Mezzio app.

Neither the Laminas, nor my own Symfony Console integration packages are required by this lib.

There's nothing to stop you from using this tool 'stand-alone' - the `./example/example.php` should point you in the
right direction for this.

At the moment, the tool has one command, `primo:build` which can generate JSON files from PHP sources that you can copy
and paste into [Prismic.io's](https://prismic.io) custom type editor.

The lib currently lacks documentation and a decent test suite but there is an annotated example in `./example`. When it comes to 
configuring as part of a Mezzio app, please examine `./src/ConfigProvider.php` for more information.

This lib is not a replacement for JS cli tooling provided by Prismic…
