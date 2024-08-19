<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit; // Exit if accessed directly
}

global $wpdb;
$table_name = $wpdb->prefix . 'blocked_ips';
$wpdb->query("DROP TABLE IF EXISTS $table_name");

delete_option('bait_user_username');
delete_option('bait_user_whitelist_ips');
delete_option('bait_user_blocked_ip_ranges');
delete_option('bait_user_htaccess_enabled');
