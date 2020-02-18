# bedrock

A foundation for setting up new Wordpress sites, highly influenced by
<https://github.com/roots/bedrock>

## Installation

Copy the contents of this repo to the new site and run:

	composer install
	composer run-script post-install
	composer run-script create-db

Adjust .env to configure the site.

## Add plugins

```
composer require wpackagist-plugin/safe-redirect-manager
composer require wpackagist-plugin/simple-page-ordering
composer require wpackagist-plugin/members

composer require wpackagist-plugin/query-monitor
wp plugin activate query-monitor

composer require wpackagist-plugin/debug-bar wpackagist-plugin/debug-bar-timber
wp plugin activate debug-bar debug-bar-timber
```

## Updates

	composer update
	wp core update-db


## Run Tests

	composer test

## Tried Plugins

- stoutlogic/acf-builder
- wpackagist-plugin/acf-to-rest-api
- wpackagist-plugin/advanced-custom-fields
- wpackagist-plugin/disable-comments
- wpackagist-plugin/html-forms
- wpackagist-plugin/piklist
- htmlburger/carbon-fields-plugin
- htmlburger/carbon-fields

  "postcss": {
    "plugins": {
      "cssnano": {},
      "postcss-normalize": {},
      "postcss-preset-env": {},
      "autoprefixer": {}
    }
  },
