# Installation

## Via composer

To get the latest stable release, check the versions at [Packagist](http://packagist.org/hollodotme/treemdown) and add to your `composer.json`:

```json
{
	"require": {
		"hollodotme/treemdown": "~1.0"
	}
}
```

To get the bleeding edge version add this to your `composer.json`:

```json
{
	"require": {
		"hollodotme/treemdown": "dev-master"
	}
}
```

**Hint:** Use `composer install/update -o` to generate a faster autoloading class map.

Now include composer's `vendor/autoload.php` into your bootstrap file and get started.

## Via GutHub

 * Check out the [latest release on GitHub](https://github.com/hollodotme/TreeMDown/releases/latest) or
 * Clone the current master with `git clone https://github.com/hollodotme/TreeMDown.git`

The application code is located under the `dist` directory and is [PSR-0](http://www.php-fig.org/psr/psr-0/) compliant.
So just register a [PSR-4](http://www.php-fig.org/psr/psr-4/) compliant auto loader with the dist directory as its root.

The used root namespace is `hollodotme\TreeMDown`.

**Note:** Don't forget to install the required dependency of [Parsedown Extra](https://github.com/erusev/parsedown-extra).
