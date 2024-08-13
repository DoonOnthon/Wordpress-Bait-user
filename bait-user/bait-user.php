<?php
/*
 * Plugin Name:       Bait-user
 * Plugin URI:        https://github.com/DoonOnthon/bait-user
 * Description:       Ban IP user when they try to login to the bait account.
 * Version:           1.2.1
 * Requires at least: 6.6.1
 * Tested up to:      6.6.1
 * Author:            DoonOnthon / Dean
 * Author URI:        https://github.com/DoonOnthon
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://github.com/DoonOnthon/bait-user
 * Text Domain:       Bait-user
 */

// Add a Settings link to the plugin's action links in the plugins list
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'bait_user_add_action_links');

function bait_user_add_action_links($links) {
    $settings_link = '<a href="admin.php?page=bait_user_settings">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}
// Add custom plugin row meta to include 'Tested up to' and 'Requires at least'
add_filter('plugin_row_meta', 'add_custom_plugin_meta', 10, 2);

function add_custom_plugin_meta($plugin_meta, $plugin_file) {
    if ($plugin_file === plugin_basename(__FILE__)) {
        $plugin_meta[] = 'Tested up to: 6.6.1';
    }
    return $plugin_meta;
}
// Hook into the activation action to check and create the table
register_activation_hook(__FILE__, 'table_check_BU');

// Function to check and create the table if it doesn't exist
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

// Hook into the login_form action to display the IP address
add_action('login_form', 'display_user_ip');

function display_user_ip() {
    if (isset($_SESSION['show_ip']) && $_SESSION['show_ip']) {
        $user_ip = $_SERVER['REMOTE_ADDR'];
        echo '<p style="color: red;">User IP: ' . esc_html($user_ip) . '</p>';
        unset($_SESSION['show_ip']);
    }
}

// Hook into the authenticate action to check for the bait user
add_action('wp_authenticate', 'check_login_for_test', 10, 2);

function check_login_for_test($username, $password) {
    global $wpdb;

    $bait_user = get_option('bait_user_username');

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if ($username === $bait_user) {
        $_SESSION['show_ip'] = true;

        $user_ip = $_SERVER['REMOTE_ADDR'];
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

        // Automatically update .htaccess if enabled
        if (get_option('bait_user_htaccess_enabled')) {
            update_htaccess_with_blocked_ips();
        }
    }
}

// Hook into WordPress init to check for blocked IPs
add_action('init', 'block_ip_if_needed');

function block_ip_if_needed() {
    global $wpdb;

    $user_ip = $_SERVER['REMOTE_ADDR'];
    $table_name = $wpdb->prefix . 'blocked_ips';
    $is_blocked = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE ip_address = %s", $user_ip));

    if ($is_blocked > 0) {
        header('HTTP/1.0 403 Forbidden');
        exit('Your IP has been blocked.');
    }
}

// Hook into the admin_menu action to add a top-level menu
add_action('admin_menu', 'bait_user_add_admin_menu');

function bait_user_add_admin_menu() {
    add_menu_page(
        'Bait User Settings',
        'Bait User',
        'manage_options',
        'bait_user_settings',
        'bait_user_settings_page',
        'dashicons-shield-alt',
        20
    );
}

// Function to display the content of the settings page
function bait_user_settings_page() {
    global $wpdb;

    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_POST['bait_user_username'])) {
        update_option('bait_user_username', sanitize_text_field($_POST['bait_user_username']));
        update_option('bait_user_htaccess_enabled', isset($_POST['bait_user_htaccess_enabled']) ? 1 : 0);
        echo '<div class="notice notice-success is-dismissible"><p>Settings have been updated.</p></div>';
    }

    $saved_bait_user = get_option('bait_user_username', '');
    $htaccess_enabled = get_option('bait_user_htaccess_enabled', 0);
    $users = get_users();
    $limit = 500;
    $table_name = $wpdb->prefix . 'blocked_ips';
    $blocked_ips = $wpdb->get_results("SELECT ip_address, blocked_at FROM $table_name ORDER BY blocked_at DESC LIMIT $limit");

    echo '<div class="wrap">';
    echo '<h1><span class="dashicons dashicons-shield-alt"></span> Bait User Plugin Settings</h1>';
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
    
    echo '</table>';
    echo '<p class="submit"><input type="submit" class="button button-primary" value="Save Changes" /></p>';
    echo '</form>';

    echo '<hr>';

    echo '<h2>Banned IP Addresses</h2>';
    echo '<p>Showing Latest ' . $limit . ' Entries</p>';
    
    if (!empty($blocked_ips)) {
        echo '<table class="widefat fixed striped">';
        echo '<thead>';
        echo '<tr>';
        echo '<th scope="col" class="manage-column">IP Address</th>';
        echo '<th scope="col" class="manage-column">Blocked At</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ($blocked_ips as $ip) {
            echo '<tr>';
            echo '<td>' . esc_html($ip->ip_address) . '</td>';
            echo '<td>' . esc_html($ip->blocked_at) . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '<p>To view all banned IP addresses, please access the database directly.</p>';
    } else {
        echo '<p>No IP addresses have been banned yet.</p>';
    }

    echo '<hr>';

    // .htaccess Instructions
    echo '<h2>Manually Block IPs at the Server Level with .htaccess</h2>';
    echo '<p><strong>Important:</strong> Adding IP addresses to your .htaccess file will block them from accessing your site entirely, even before they reach WordPress. Only proceed if you know what you are doing. Always make a backup of your .htaccess file before editing it.</p>';
    echo '<p>Copy and paste the following lines into your .htaccess file to block the IPs manually:</p>';

    if (!empty($blocked_ips)) {
        echo '<textarea readonly style="width: 100%; height: 200px; font-family: monospace;">';
        echo "# BEGIN Bait User IP Block\n";
        echo "# This is part of the Bait User plugin. Do not delete this unless you know what you're doing.\n";

        foreach ($blocked_ips as $ip) {
            echo "Deny from " . esc_html($ip->ip_address) . "\n";
        }

        echo "# END Bait User IP Block\n";
        echo '</textarea>';
    } else {
        echo '<p>No IP addresses to block at the moment.</p>';
    }

    echo '</div>';
}

// Function to update the .htaccess file with blocked IPs
function update_htaccess_with_blocked_ips() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'blocked_ips';
    $blocked_ips = $wpdb->get_results("SELECT ip_address FROM $table_name");

    // Prepare the content to be added to .htaccess
    $htaccess_content = "# BEGIN Bait User IP Block\n";
    $htaccess_content .= "# This is part of the Bait User plugin. Do not delete this unless you know what you're doing.\n";

    foreach ($blocked_ips as $ip) {
        $htaccess_content .= "Deny from " . esc_html($ip->ip_address) . "\n";
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
            error_log('Successfully updated .htaccess with blocked IPs.');
        }
    } else {
        error_log('Failed to write to .htaccess file. Please check file permissions.');
    }
}
