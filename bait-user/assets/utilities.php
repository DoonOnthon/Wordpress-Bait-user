<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function ip_in_range($ip, $range) {
    // Check if the range contains a valid CIDR netmask (i.e., the '/' character)
    if (strpos($range, '/') === false) {
        // If no netmask is provided, assume it's a single IP address
        return $ip === $range;
    }

    list($range, $netmask) = explode('/', $range, 2);

    // Convert IP and range to their decimal representations
    $range_decimal = ip2long($range);
    $ip_decimal = ip2long($ip);

    // Calculate wildcard and netmask decimals
    $wildcard_decimal = pow(2, (32 - $netmask)) - 1;
    $netmask_decimal = ~$wildcard_decimal;

    // Check if the IP is in the range
    return ($ip_decimal & $netmask_decimal) == ($range_decimal & $netmask_decimal);
}

function update_htaccess_with_blocked_ips() {
    global $wpdb;

    $whitelist_ips = get_option('bait_user_whitelist_ips', []);
    $table_name = $wpdb->prefix . 'blocked_ips';
    $blocked_ips = $wpdb->get_results("SELECT ip_address FROM $table_name");

    // Get manually blocked IP ranges
    $blocked_ip_ranges = get_option('bait_user_blocked_ip_ranges', []);

    // Prepare the content to be added to .htaccess
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

    // Define the path to the .htaccess file
    $htaccess_file = ABSPATH . '.htaccess';
    
    if (is_writable($htaccess_file)) {
        $current_htaccess = file_get_contents($htaccess_file);
        
        // Remove any existing Bait User block
        $new_htaccess = preg_replace('/# BEGIN Bait User IP Block.*# END Bait User IP Block/s', '', $current_htaccess);
        
        // Add the new content
        $new_htaccess .= "\n" . $htaccess_content;

        // Write the updated content back to .htaccess
        if (file_put_contents($htaccess_file, $new_htaccess) === false) {
            error_log('Failed to write to .htaccess file.');
        } else {
            error_log('Successfully updated .htaccess with blocked IPs and ranges.');
        }
    } else {
        error_log('Failed to write to .htaccess file. Please check file permissions.');
    }
}
