<?php

/**
 * The plugin bootstrap.
 *
 * @link              https://www.kiener.nl
 * @since             1.0.0
 * @package           Tradecast
 *
 * @wordpress-wp-plugin
 * Plugin Name:       Tradecast
 * Plugin URI:        https://www.tradecast.tv
 * Description:       Add Tradecast videos and galleries to your content using this wp-plugin.
 * Version:           1.0.1
 * Author:            Kiener Digital Commerce
 * Author URI:        https://www.kiener.nl
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tradecast
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die();
}

/**
 * Defines plugin attributes.
 */
define('TRADECAST_PLUGIN_NAME', 'tradecast');
define('TRADECAST_PLUGIN_VERSION', '1.0.1');

/**
 * Handles plugin activation.
 */
function activate_tradecast()
{
    require_once plugin_dir_path(__FILE__) . 'includes' . DIRECTORY_SEPARATOR . 'class-tradecast-activator.php';
    Tradecast_Activator::activate();
}

/**
 * Handles plugin deactivation.
 */
function deactivate_tradecast()
{
    require_once plugin_dir_path(__FILE__) . 'includes' . DIRECTORY_SEPARATOR . 'class-tradecast-deactivator.php';
    Tradecast_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_tradecast');
register_deactivation_hook(__FILE__, 'deactivate_tradecast');

/**
 * Loads the core class dependency.
 */
require plugin_dir_path(__FILE__) . 'includes' . DIRECTORY_SEPARATOR . 'class-tradecast.php';

/**
 * Begins execution of the WordPress plugin.
 *
 * @since 1.0.0
 * @access public
 */
function run_tradecast()
{
    $plugin = new Tradecast();
    $plugin->run();
}

run_tradecast();
