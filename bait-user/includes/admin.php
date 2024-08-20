<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Add a Settings link to the plugin's action links in the plugins list
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'bait_user_add_action_links');

function bait_user_add_action_links($links)
{
    $settings_link = '<a href="admin.php?page=bait_user_settings">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}

// Add custom plugin row meta to include 'Tested up to' and 'Requires at least'
add_filter('plugin_row_meta', 'add_custom_plugin_meta', 10, 2);

function add_custom_plugin_meta($plugin_meta, $plugin_file)
{
    if ($plugin_file === plugin_basename(__FILE__)) {
        $plugin_meta[] = 'Tested up to: 6.6.1';
    }
    return $plugin_meta;
}

// Add admin menu
add_action('admin_menu', 'bait_user_add_admin_menu');
function bait_user_add_admin_menu()
{
    add_menu_page(
        'Bait User Settings',
        'Bait User',
        'manage_options',
        'bait_user_settings',
        'bait_user_settings_page',
        'dashicons-shield-alt',
        20
    );
    // Submenu for Logs
    add_submenu_page(
        'bait_user_settings',
        'Bait User Logs',
        'Logs',
        'manage_options',
        'bait_user_logs',
        'bait_user_logs_page'
    );
    // Submenu for help
    add_submenu_page(
        'bait_user_settings',
        'Bait User Help',
        'Help',
        'manage_options',
        'bait_user_help',
        'bait_user_help_page'
    );
}
function bait_user_settings_page()
{
    global $wpdb;

    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_POST['bait_user_username'])) {
        update_option('bait_user_username', sanitize_text_field($_POST['bait_user_username']));
        update_option('bait_user_htaccess_enabled', isset($_POST['bait_user_htaccess_enabled']) ? 1 : 0);
        echo '<div class="notice notice-success is-dismissible"><p>Settings have been updated.</p></div>';
    }

    // Handle form submission for saving the whitelist IPs
    if (isset($_POST['bait_user_whitelist_ips'])) {
        $whitelist_ips = array_map('trim', explode("\n", sanitize_textarea_field($_POST['bait_user_whitelist_ips'])));
        update_option('bait_user_whitelist_ips', array_filter($whitelist_ips));
        echo '<div class="notice notice-success is-dismissible"><p>Whitelist IPs have been updated.</p></div>';
    }

    // Handle form submission for saving blocked IP ranges
    if (isset($_POST['bait_user_blocked_ip_ranges'])) {
        $blocked_ip_ranges = array_map('trim', explode("\n", sanitize_textarea_field($_POST['bait_user_blocked_ip_ranges'])));
        update_option('bait_user_blocked_ip_ranges', array_filter($blocked_ip_ranges));
        echo '<div class="notice notice-success is-dismissible"><p>Blocked IP Ranges have been updated.</p></div>';
    }

    $saved_bait_user = get_option('bait_user_username', '');
    $htaccess_enabled = get_option('bait_user_htaccess_enabled', 0);
    $saved_whitelist_ips = get_option('bait_user_whitelist_ips', []);
    $blocked_ip_ranges = get_option('bait_user_blocked_ip_ranges', []);
    $users = get_users();
    $limit = 500;
    $table_name = $wpdb->prefix . 'blocked_ips';
    $blocked_ips = $wpdb->get_results("SELECT ip_address, blocked_at FROM $table_name ORDER BY blocked_at DESC LIMIT $limit");

    echo '<div class="wrap">';
    echo '<h1><span class="dashicons dashicons-shield-alt"></span> Bait User Plugin Settings</h1>';
    echo '<hr>';

    // Add buttons for navigation to Logs and other sections
    echo '<div class="custom-admin-buttons">';
    echo '<a href="admin.php?page=bait_user_settings" class="button">Settings</a>';
    echo '<a href="admin.php?page=bait_user_logs" class="button button-primary">View Logs</a>';
    echo '<a href="admin.php?page=bait_user_help" class="button">Help</a>';
    echo '</div>';

    echo '<hr>';

    echo '<form method="POST" action="" style="max-width: 600px;">';
    echo '<h2>User Settings</h2>';
    echo '<table class="form-table">';

    echo '<tr>';
    echo '<th scope="row"><label for="bait_user_username">Select Bait User:</label></th>';
    echo '<td>';
    echo '<select name="bait_user_username" id="bait_user_username" class="regular-text">';

    foreach ($users as $user) {
        $selected = ($user->user_login == $saved_bait_user) ? 'selected="selected"' : '';
        echo '<option value="' . esc_attr($user->user_login) . '" ' . $selected . '>' . esc_html($user->user_login) . '</option>';
    }

    echo '</select>';
    echo '</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<th scope="row"><label for="bait_user_htaccess_enabled">Enable Automatic .htaccess IP Blocking:</label></th>';
    echo '<td>';
    echo '<input type="checkbox" name="bait_user_htaccess_enabled" id="bait_user_htaccess_enabled" value="1" ' . checked(1, $htaccess_enabled, false) . ' />';
    echo '<p class="description">Only enable if you know what you are doing.</p>';
    echo '</td>';
    echo '</tr>';

    // Whitelist IPs field
    echo '<tr>';
    echo '<th scope="row"><label for="bait_user_whitelist_ips">Whitelist IPs:</label></th>';
    echo '<td>';
    echo '<textarea name="bait_user_whitelist_ips" rows="10" cols="50" class="large-text code">' . esc_textarea(implode("\n", $saved_whitelist_ips)) . '</textarea>';
    echo '<p class="description">Enter one IP address per line that should not be blocked, even if it logs into the bait account.</p>';
    echo '</td>';
    echo '</tr>';

    // Blocked IP Ranges field
    echo '<tr>';
    echo '<th scope="row"><label for="bait_user_blocked_ip_ranges">Blocked IP Ranges:</label></th>';
    echo '<td>';
    echo '<textarea name="bait_user_blocked_ip_ranges" rows="10" cols="50" class="large-text code">' . esc_textarea(implode("\n", $blocked_ip_ranges)) . '</textarea>';
    echo '<p class="description">Enter one IP range per line (e.g., 192.168.1.0/24) that should be blocked.</p>';
    echo '</td>';
    echo '</tr>';

    echo '</table>';
    echo '<p class="submit"><input type="submit" class="button button-primary" value="Save Changes" /></p>';

    echo '</form>';

    echo '<hr>';

    echo '<h2>Banned IP Addresses</h2>';
    echo '<p>Showing Latest ' . $limit . ' Entries</p>';

    if (!empty($blocked_ips)) {
        echo '<form method="POST" action="">';
        echo '<table class="widefat fixed striped">';
        echo '<thead>';
        echo '<tr>';
        echo '<th scope="col" class="manage-column">IP Address</th>';
        echo '<th scope="col" class="manage-column">Blocked At</th>';
        echo '<th scope="col" class="manage-column">Action</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ($blocked_ips as $ip) {
            echo '<tr>';
            echo '<td>' . esc_html($ip->ip_address) . '</td>';
            echo '<td>' . esc_html($ip->blocked_at) . '</td>';
            echo '<td>';
            echo '<button type="submit" name="remove_blocked_ip" value="' . esc_attr($ip->ip_address) . '" class="button-secondary">Remove</button>';
            echo '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '</form>';
        echo '<p>To view all banned IP addresses, please access the database directly.</p>';
    } else {
        echo '<p>No IP addresses have been banned yet.</p>';
    }

    echo '<hr>';

    // .htaccess Instructions
    echo '<h2>Manually Block IPs at the Server Level with .htaccess</h2>';
    echo '<p><strong>Important:</strong> Adding IP addresses to your .htaccess file will block them from accessing your site entirely, even before they reach WordPress. Only proceed if you know what you are doing. Always make a backup of your .htaccess file before editing it.</p>';
    echo '<p>Copy and paste the following lines into your .htaccess file to block the IPs manually:</p>';

    if (!empty($blocked_ips) || !empty($blocked_ip_ranges)) {
        echo '<textarea readonly style="width: 100%; height: 200px; font-family: monospace;">';
        echo "# BEGIN Bait User IP Block\n";
        echo "# This is part of the Bait User plugin. Do not delete this unless you know what you're doing.\n";

        foreach ($blocked_ips as $ip) {
            echo "Deny from " . esc_html($ip->ip_address) . "\n";
        }

        foreach ($blocked_ip_ranges as $range) {
            echo "Deny from " . esc_html($range) . "\n";
        }

        echo "# END Bait User IP Block\n";
        echo '</textarea>';
    } else {
        echo '<p>No IP addresses or ranges to block at the moment.</p>';
    }

    echo '</div>';
}
//Create user logs page
function bait_user_logs_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Get the selected log type from the URL
    $selected_log_type = isset($_GET['log_type']) ? sanitize_text_field($_GET['log_type']) : 'login';

    echo '<div class="wrap">';
    echo '<h1><span class="dashicons dashicons-shield-alt"></span> Bait User Logs</h1>';

    // Dropdown to select log type
    echo '<form method="GET" action="">';
    echo '<input type="hidden" name="page" value="bait_user_logs" />';
    echo '<select name="log_type" onchange="this.form.submit()">';
    echo '<option value="login" ' . selected($selected_log_type, 'login', false) . '>Login Logs</option>';
    echo '<option value="error" ' . selected($selected_log_type, 'error', false) . '>Error Logs</option>';
    echo '<option value="activity" ' . selected($selected_log_type, 'activity', false) . '>Activity Logs</option>';
    echo '</select>';
    echo '</form>';

    echo '<hr>';

    // Display the logs based on the selected type
    bait_user_display_logs($selected_log_type);

    echo '</div>';
}

function bait_user_display_logs($log_type) {
    global $wpdb;
    
    // Set a limit for the number of logs to display
    $limit = 100;
    $logs = [];

    switch ($log_type) {
        case 'login':
            echo '<h2>Login Logs</h2>';
            // Example query to get login logs from the database
            $logs = $wpdb->get_results("SELECT log_time, ip_address, details FROM {$wpdb->prefix}login_logs ORDER BY log_time DESC LIMIT $limit");
            break;

        case 'error':
            echo '<h2>Error Logs</h2>';
            // Example query to get error logs from the database
            $logs = $wpdb->get_results("SELECT log_time, details FROM {$wpdb->prefix}error_logs ORDER BY log_time DESC LIMIT $limit");
            break;

        case 'activity':
            echo '<h2>Activity Logs</h2>';
            // Example query to get activity logs from the database
            $logs = $wpdb->get_results("SELECT log_time, details FROM {$wpdb->prefix}activity_logs ORDER BY log_time DESC LIMIT $limit");
            break;

        default:
            echo '<p>No logs found for this log type.</p>';
            return; // Return early if the log type is invalid
    }

    // Call the display function with the retrieved logs
    bait_user_display_log_table($logs);
}

function bait_user_display_log_table($logs) {
    if (empty($logs)) {
        echo '<p>No logs found.</p>';
        return;
    }

    // Display the logs in a table
    echo '<table class="widefat fixed striped">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Date/Time</th>';
    echo '<th>Details</th>';
    echo '<th>IP Address</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    foreach ($logs as $log) {
        echo '<tr>';
        echo '<td>' . esc_html($log->log_time) . '</td>';
        echo '<td>' . esc_html($log->details) . '</td>';
        echo '<td>' . esc_html($log->ip_address) . '</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
}
function bait_user_help_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    echo '<h1><span class="dashicons dashicons-shield-alt"></span> Bait User Help Page</h1>';

}