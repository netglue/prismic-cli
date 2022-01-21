# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 1.1.0 - 2022-01-21

### Added

- [#49](https://github.com/netglue/prismic-cli/pull/49) Allows the slice display property to be used in the TypeBuilder

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#48](https://github.com/netglue/prismic-cli/pull/48) Fixes psalm errors due to the deprecation of bookmarks in [`prismic-client`](https://github.com/netglue/prismic-client)

## 1.0.3 - 2021-12-16

### Added

- [#31](https://github.com/netglue/prismic-cli/pull/31) Adds workflow to automate dependabot upgrades
- [#32](https://github.com/netglue/prismic-cli/pull/32) Adds a workflow to check for unused and missing composer dependencies

### Changed

- [#30](https://github.com/netglue/prismic-cli/pull/30) Allows installation with Symfony Console ^6

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.0.2 - 2021-10-08

### Added

- Nothing.

### Changed

- Bumps `netglue/prismic-client` to `^1.0.0` which is not a BC break because only the version number has changed indicating the first stable.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.0.1 - 2021-10-07

### Added

- [#14](https://github.com/netglue/prismic-cli/pull/14) Adds PHP 8.1 support thanks to @gsteel

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.0.0 - 2021-10-01

### Added

- Nothing.

### Changed

- Updates locked deps.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.4.2 - 2021-09-02

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#7](https://github.com/netglue/prismic-cli/pull/7) makes sure that dependencies are loadable when the user opts out of the custom type api tooling.

## 0.4.1 - 2021-09-02

### Added

- Nothing.

### Changed

- [#6](https://github.com/netglue/prismic-cli/pull/6) adds config.select = null to links that do not have to be a specific type to match the structure returned from the api. This helps to reduce noise in diffs…

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.4.0 - 2021-09-02

### Added

- `primo:types:download` - A console command that can download all document type definitions to local storage using the Prismic Custom Types API.
- `primo:types:upload` - A console command that can upload local document type definitions to the Prismic Custom Types API.
- `primo:types:diff` - A console command that can show a colour diff between local and remote document type definitions.
- To facilitate the commands above, a new persistence abstraction has been introduced for local and remote storage, including a dependency on [`netglue/prismic-doctype-client`](https://github.com/netglue/prismic-doctype-client).

### Changed

- Factories that produce some kind of HTTP API Client, now make use of [`php-http/discovery`](https://github.com/php-http/discovery) for finding dependencies, but still prefer those that have been configured in the container.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.3.0 - 2021-03-03

### Added

- Nothing.

### Changed

- Switch CI workflows.

### Deprecated

- Nothing.

### Removed

- The Api Info command no longer lists all the known tags - this is getting deprecated/removed from the initial api response.

### Fixed

- Nothing.

## 0.2.0 - 2020-10-16

### Added

- New commands that help with dumping information about the currently configured repository.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.1.4 - 2020-10-16

### Added

- A smoke test to make sure that the build command runs over the examples without any errors.

### Changed

- Changed coding standard to be closer to Doctrine/Slevomat standard
- Updated the default PHPUnit config for compatibility with recent versions

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.1.3 - 2020-07-07

### Added

- Nothing.

### Changed

- TypeBuilder - add default null values to a number of methods to keep things compact.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.1.2 - 2020-06-15

### Added

- Generic link helper to the type builder.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.1.1 - 2020-06-10

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Fixes incorrect property name for default value of boolean field

## 0.1.0 - 2020-06-09

### Added

- Initial command, examples and container config etc.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.
