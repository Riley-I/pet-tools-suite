<?php
/**
 * Plugin Name: Pet Tools Suite
 * Description: Modular WordPress tools built for performance and scale.
 * Version: 0.1.0
 * Author: Riley Inniss
 */

defined('ABSPATH') || exit;

define('PETTOOLS_VERSION', '0.1.0');
define('PETTOOLS_PATH', plugin_dir_path(__FILE__));
define('PETTOOLS_URL', plugin_dir_url(__FILE__));

require_once PETTOOLS_PATH . 'vendor/autoload.php';

$plugin = new PetTools\Plugin();
$plugin->register();
