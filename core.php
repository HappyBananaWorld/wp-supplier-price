<?php

/**
 * Plugin Name: XYZ Supplier Discount
 * Plugin URI:  https://example.com/
 * Description: Adds supplier discount percentage field to WooCommerce products.
 * Version:     1.0.0
 * Author:      Your Name
 * License:     GPLv2 or later
 * Text Domain: xyz-supplier-discount
 */
if (!defined('ABSPATH')) {
    exit;
}


// Constants
define('XYZSP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('XYZSP_PLUGIN_FILE', __FILE__);


// Require composer autoload if exists
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    // Fallback: simple autoloader for PSR-4 limited to XYZSP namespace
    spl_autoload_register(function ($class) {
        $prefix = 'XYZSP\\';
        $base_dir = __DIR__ . '/src/';
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }
        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        if (file_exists($file))
            require $file;
    });
}


// Boot plugin
register_activation_hook( __FILE__, ['XYZSP\Core\Plugin', 'activate'] );
XYZSP\Core\Plugin::instance(XYZSP_PLUGIN_FILE)->run();