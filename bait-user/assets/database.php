<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function table_check_BU() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'blocked_ips';

    $table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name));

    if ($table_exists != $table_name) {
        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            ip_address VARCHAR(100) NOT NULL,
            blocked_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY (id)
        ) {$wpdb->get_charset_collate()};";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Table created: ' . $table_name);
        }
    } else {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Table already exists: ' . $table_name);
        }
    }
}
