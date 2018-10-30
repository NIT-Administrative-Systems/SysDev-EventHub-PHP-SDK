# EventHub PHP SDK
This is a set of PHP classes design to give you easy access to the new Northwestern EventHub & AMQ.

## Installation
This package is [not yet actually] available via composer:

```sh
$ composer require northwestern-sysdev/event-hub-php-sdk
```

You will need to contact ET-I&A in order to set up your access to EventHub.

## Contributing
Submit a pull request!

If you need to include a local copy of the package for development purposes, adjust your consuming apps' `composer.json` thusly:

```js
{
    // Add this section
    "repositories": [
        {
            "type": "path",
            "url": "/home/vagrant/code/SysDev-EventHub-PHP-SDK"
        }
    ],

    "require": {
        // Any branch that isn't named in a version format can be specified by prefixing
        // it with 'dev-', so this would install the 'my-feature' branch from a local copy of the package.
        "northwestern-sysdev/event-hub-php-sdk": "dev-my-feature"
    },
}
```

You can test the package by running `phpunit`.
