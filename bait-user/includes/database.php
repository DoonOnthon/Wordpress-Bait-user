<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function table_check_BU()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Table for blocked IPs
    $blocked_ips_table = $wpdb->prefix . 'blocked_ips';
    $blocked_ips_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $blocked_ips_table));

    if ($blocked_ips_exists != $blocked_ips_table) {
        $blocked_ips_sql = "CREATE TABLE $blocked_ips_table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            ip_address VARCHAR(100) NOT NULL,
            blocked_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($blocked_ips_sql);

        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Table created: ' . $blocked_ips_table);
        }
    } else {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Table already exists: ' . $blocked_ips_table);
        }
    }
    // Table for login logs
    $login_logs_table = $wpdb->prefix . 'login_logs';
    $login_logs_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $login_logs_table));

    if ($login_logs_exists != $login_logs_table) {
        $login_logs_sql = "CREATE TABLE $login_logs_table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            ip_address VARCHAR(100) NOT NULL,
            log_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
            details TEXT DEFAULT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($login_logs_sql);

        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Table created: ' . $login_logs_table);
        }
    } else {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Table already exists: ' . $login_logs_table);
        }
    }
        // Table for activty logs
        $activity_logs_table = $wpdb->prefix . 'activity_logs';
        $activity_logs_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $activity_logs_table));
    
        if ($activity_logs_exists != $activity_logs_table) {
            $activty_logs_sql = "CREATE TABLE $activity_logs_table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            activity_description TEXT NOT NULL,
            ip_address VARCHAR(100) NOT NULL,
            log_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";
    
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($activty_logs_sql);
    
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Table created: ' . $activity_logs_table);
            }
        } else {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Table already exists: ' . $activity_logs_table);
            }
        }
}
