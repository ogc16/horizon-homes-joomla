<?php
/**
 * PHPStan bootstrap.
 *
 * A Joomla extension cannot resolve the CMS classes on its own; the framework
 * is autoloaded at runtime from the Joomla installation (which is not a
 * Composer dependency). This bootstrap reproduces that: it loads Composer's
 * autoloader and registers the PSR-4 map Joomla generates for its extensions.
 *
 * Set JOOMLA_ROOT when running outside the Docker image.
 */

$root = getenv('JOOMLA_ROOT') ?: '/var/www/html';

if (!defined('_JEXEC')) {
    define('_JEXEC', 1);
}

$constants = [
    'JPATH_BASE'          => $root,
    'JPATH_ROOT'          => $root,
    'JPATH_SITE'          => $root,
    'JPATH_ADMINISTRATOR' => $root . '/administrator',
    'JPATH_API'           => $root . '/api',
    'JPATH_LIBRARIES'     => $root . '/libraries',
    'JPATH_PLUGINS'       => $root . '/plugins',
    'JPATH_MANIFESTS'     => $root . '/administrator/manifests',
    'JPATH_CACHE'         => $root . '/administrator/cache',
    'JPATH_CONFIGURATION' => $root,
];

foreach ($constants as $name => $value) {
    if (!defined($name)) {
        define($name, $value);
    }
}

$loader = require $root . '/libraries/vendor/autoload.php';

$mapFile = $root . '/administrator/cache/autoload_psr4.php';

if (is_file($mapFile)) {
    foreach (require $mapFile as $namespace => $paths) {
        foreach ((array) $paths as $path) {
            $loader->addPsr4($namespace, $path);
        }
    }
}
