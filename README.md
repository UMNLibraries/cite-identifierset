# cite-identifierset

A PHP package for EthicShare for citation de-duplication. Must run both in and out of Drupal.

## Installing

Install via [Composer](http://getcomposer.org). In your project's `composer.json`:

```json
  "require": {
    "umnlib/cite-identifierset": "1.0.*"
  },
  "repositories": [
    {
      "type": "vcs",
      "url": "git@github.com:UMNLibraries/cite-identifierset.git"
    }
  ]
```

## System Dependencies

This package requires a MySQL server, client, and the PHP MySQL extensions.

## Running the Tests

Running the PHPUnit tests requires configuration. Notice that `phpunit.xml.dist` contains places to put your MySQL credentials. Do not modify that file! Instead, copy the file to `phpunit.xml`, which will override `phpunit.xml.dist`, and insert your credentials into that file. This repository is configured to ignore `phpunit.xml`, which helps to prevent exposing sensitive information, like passwords, to public source control repositories.

## Older Versions

For older versions of this package that did not use Composer, see the `0.x.y` releases.

## Attribution

The University of Minnesota Libraries created this software for the [EthicShare](http://www.ethicshare.org/about) project.
