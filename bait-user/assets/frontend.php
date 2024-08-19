<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

add_action('init', 'block_ip_if_needed');
add_action('wp_authenticate', 'check_login_for_test', 10, 2);
add_action('login_form', 'display_user_ip');  // Add this action

function block_ip_if_needed() {
    global $wpdb;

    $user_ip = $_SERVER['REMOTE_ADDR'];
    $table_name = $wpdb->prefix . 'blocked_ips';

    // Check if the IP is in the block list
    $is_blocked = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE ip_address = %s", $user_ip));

    // Also check if the IP is within any blocked ranges
    $blocked_ranges = get_option('bait_user_blocked_ip_ranges', []);
    foreach ($blocked_ranges as $range) {
        if (ip_in_range($user_ip, $range)) {
            $is_blocked++;
            break;
        }
    }

    if ($is_blocked > 0) {
        header('HTTP/1.0 403 Forbidden');
        exit('Your IP has been blocked.');
    }
}

function check_login_for_test($username, $password) {
    global $wpdb;

    $bait_user = get_option('bait_user_username');
    $whitelist_ips = get_option('bait_user_whitelist_ips', []);

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $user_ip = $_SERVER['REMOTE_ADDR'];

    // Check if the IP is in the whitelist
    if (in_array($user_ip, $whitelist_ips)) {
        return; // Do nothing if the IP is whitelisted
    }

    if ($username === $bait_user) {
        $_SESSION['show_ip'] = true;

        $table_name = $wpdb->prefix . 'blocked_ips';

        $wpdb->insert(
            $table_name,
            array(
                'ip_address' => $user_ip,
                'blocked_at' => current_time('mysql')
            ),
            array(
                '%s',
                '%s'
            )
        );

        if (get_option('bait_user_htaccess_enabled')) {
            update_htaccess_with_blocked_ips();
        }
    }
}

function display_user_ip() {
    if (isset($_SESSION['show_ip']) && $_SESSION['show_ip']) {
        $user_ip = $_SERVER['REMOTE_ADDR'];
        echo '<p style="color: red;">User IP: ' . esc_html($user_ip) . '</p>';
        unset($_SESSION['show_ip']);
    }
}
