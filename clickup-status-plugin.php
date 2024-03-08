<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @since             1.0.0
 * @package           ClickUp Status Plugin
 *
 * @wordpress-plugin
 * Plugin Name:       ClickUp Status Plugin
 * Description:       This plugin enable the admin to connect Gravity Forms to Click UP and let the users to track the status of the form they have submitted
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.2
 * Author:            Amir Jamali
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Languages: 
 * Text Domain:       clickup-status-plugin
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Setup
define('CSP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CSP_PLUGIN_FILE', __FILE__);

// Include Composer autoloader
require_once CSP_PLUGIN_DIR. '/src/ClickUpStatusPlugin.php';


function csp_load_textdomain() {
    load_plugin_textdomain('clickup-status-plugin', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'csp_load_textdomain');
