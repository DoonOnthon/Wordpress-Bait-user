<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function ip_in_range($ip, $range) {
    if (strpos($range, '/') === false) {
        return $ip === $range;
    }

    list($range, $netmask) = explode('/', $range, 2);
    $range_decimal = ip2long($range);
    $ip_decimal = ip2long($ip);
    $wildcard_decimal = pow(2, (32 - $netmask)) - 1;
    $netmask_decimal = ~$wildcard_decimal;

    return ($ip_decimal & $netmask_decimal) == ($range_decimal & $netmask_decimal);
}

function update_htaccess_with_blocked_ips() {
    global $wpdb;

    $whitelist_ips = get_option('bait_user_whitelist_ips', []);
    $table_name = $wpdb->prefix . 'blocked_ips';
    $blocked_ips = $wpdb->get_results("SELECT ip_address FROM $table_name");

    $blocked_ip_ranges = get_option('bait_user_blocked_ip_ranges', []);

    $htaccess_content = "# BEGIN Bait User IP Block\n";
    $htaccess_content .= "# This is part of the Bait User plugin. Do not delete this unless you know what you're doing.\n";

    foreach ($blocked_ips as $ip) {
        if (!in_array($ip->ip_address, $whitelist_ips)) {
            $htaccess_content .= "Deny from " . esc_html($ip->ip_address) . "\n";
        }
    }

    foreach ($blocked_ip_ranges as $range) {
        $htaccess_content .= "Deny from " . esc_html($range) . "\n";
    }

    $htaccess_content .= "# END Bait User IP Block\n";

    $htaccess_file = ABSPATH . '.htaccess';
    
    if (is_writable($htaccess_file)) {
        $current_htaccess = file_get_contents($htaccess_file);
        
        $new_htaccess = preg_replace('/# BEGIN Bait User IP Block.*# END Bait User IP Block/s', '', $current_htaccess);
        
        $new_htaccess .= "\n" . $htaccess_content;

        if (file_put_contents($htaccess_file, $new_htaccess) === false) {
            error_log('Failed to write to .htaccess file.');
        } else {
            error_log('Successfully updated .htaccess with blocked IPs and ranges.');
        }
    } else {
        error_log('Failed to write to .htaccess file. Please check file permissions.');
    }
}

/**
 * Logs login attempts to the database.
 *
 * @param string $user_login The username of the user logging in.
 * @param WP_User $user The WP_User object for the user logging in.
 */
function log_login_attempt($user_login, $user) {
    global $wpdb;
    
    $user_id = intval($user->ID); 
    $ip_address = sanitize_text_field($_SERVER['REMOTE_ADDR']); 
    $log_time = current_time('mysql'); 
    $details = 'Login successful'; 

    $table_name = $wpdb->prefix . 'login_logs';
    $inserted = $wpdb->insert(
        $table_name,
        array(
            'user_id' => $user_id,
            'ip_address' => $ip_address,
            'log_time' => $log_time,
            'details' => $details
        ),
        array(
            '%d', 
            '%s', 
            '%s', 
            '%s'  
        )
    );

    if ($wpdb->last_error) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Failed to insert login log: ' . $wpdb->last_error);
        }
    } else {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Login log inserted successfully for user ID: ' . $user_id);
        }
    }
}

/**
 * Logs failed login attempts to the database.
 *
 * @param string $username The username of the failed login attempt.
 */
function log_failed_login($username) {
    global $wpdb;
    
    $ip_address = sanitize_text_field($_SERVER['REMOTE_ADDR']); 
    $log_time = current_time('mysql'); 
    $details = 'Login failed'; 

    $table_name = $wpdb->prefix . 'login_logs';
    $inserted = $wpdb->insert(
        $table_name,
        array(
            'user_id' => 0, // No user ID for failed logins
            'ip_address' => $ip_address,
            'log_time' => $log_time,
            'details' => $details . ' for user: ' . sanitize_text_field($username)
        ),
        array(
            '%d', 
            '%s', 
            '%s', 
            '%s'  
        )
    );

    if ($wpdb->last_error) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Failed to insert failed login log: ' . $wpdb->last_error);
        }
    } else {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Failed login log inserted successfully for username: ' . sanitize_text_field($username));
        }
    }
}

// Hook the logging functions
add_action('wp_login', 'log_login_attempt', 10, 2);
add_action('wp_login_failed', 'log_failed_login');
