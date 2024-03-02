<?php
/*
Plugin Name: ClickUp Status Plugin
Description: This plugin handles ClickUp status checks.
Version: 1.0
Author: Amir Jamali
*/


if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Setup
define('CSP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CSP_PLUGIN_FILE', __FILE__);

// Include Composer autoloader
require_once CSP_PLUGIN_DIR. '/src/ClickUpStatusPlugin.php';