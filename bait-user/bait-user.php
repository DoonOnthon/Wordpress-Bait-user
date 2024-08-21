<?php
/*
 * Plugin Name:       Bait-user
 * Plugin URI:        https://github.com/DoonOnthon/bait-user
 * Description:       Ban IP user when they try to login to the bait account.
 * Version:           1.3.0
 * Requires at least: 6.5.5
 * Tested up to:      6.6.1
 * Author:            DoonOnthon / Dean
 * Author URI:        https://github.com/DoonOnthon
 * License:           CC0 1.0 Universal
 * License URI:       https://creativecommons.org/publicdomain/zero/1.0/
 * Update URI:        https://github.com/DoonOnthon/bait-user
 * Text Domain:       Bait-user
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Include required files in the correct order
require_once plugin_dir_path(__FILE__) . 'includes/utilities.php';  // Load utilities first
require_once plugin_dir_path(__FILE__) . 'includes/admin.php';
require_once plugin_dir_path(__FILE__) . 'includes/frontend.php';
require_once plugin_dir_path(__FILE__) . 'includes/database.php';

// Activation hook
register_activation_hook(__FILE__, 'table_check_BU');
