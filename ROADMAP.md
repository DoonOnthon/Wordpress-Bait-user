# Bait-User Plugin Roadmap

*Please note: This roadmap is subject to change based on user feedback, development priorities, and evolving needs. It's intended to provide an overview of potential features and improvements for the Bait-User plugin.*

This roadmap outlines the future development plans and potential features for the Bait-User WordPress Plugin. The goal is to continually improve the plugin's functionality, security, and user experience while keeping the feature set practical and user-focused.

## Version 1.2.x

### 1. **IP Whitelisting**
   - **Whitelist Specific IPs**: Implement a feature that allows administrators to whitelist certain IP addresses, ensuring they are never blocked by the plugin, even if they attempt to log in with the bait user.

### 2. **IP Range Blocking**
   - **Block IP Ranges**: Add the ability to block entire IP ranges, providing more comprehensive security against threats originating from known problematic IP blocks.

### 3. **Logging and Reporting**
   - **Detailed Logs**: Create a logging system that records all login attempts, including successful, failed, and blocked attempts, along with IP addresses, timestamps, and usernames.
   - **Export Logs**: Implement an option to export logs to CSV or JSON format for further analysis or integration with other security tools.
     
## Version 1.3.x **WORDPRESS RELEASE**

### 4. **Geolocation-Based Blocking**
   - **Country-Level Blocking**: Integrate geolocation to block or allow access based on the country of origin of the IP address.
   - **Region-Specific Rules**: Implement custom rules that apply different security measures based on the user’s geographic region.

### 5. **Simplified Admin Interface**
   - **Redesign Settings Page**: Improve the design and layout of the settings page for a more user-friendly experience.
   - **Contextual Help**: Add tooltips and help sections directly within the settings page to assist users in configuring the plugin.
## Version 1.4.x

### 6. **Advanced Customization Options**
   - **Hooks and Filters**: Provide advanced users with hooks and filters to customize the plugin’s behavior to fit their unique security needs.
   - **Customizable Alerts**: Allow administrators to set custom alerts and actions based on specific security events, such as repeated login attempts from a single IP address.

### 7. **Multisite Network Support**
   - **Network-Wide IP Blocking**: Implement network-wide IP blocking for WordPress Multisite installations, allowing administrators to block IPs across all sites in the network from a single interface.
   - **Centralized Settings Management**: Allow network admins to manage all Bait-User plugin settings across multiple sites from one centralized location.

## Version 1.5.x and Beyond

### 8. **Cloud Integration (Future Consideration)**
   - **Cloud-Based IP Blocking**: Integrate with cloud security services to block known malicious IPs in real-time across multiple sites using the plugin.
   - **Centralized Logging**: Allow centralized logging and management of blocked IPs across multiple WordPress sites through a cloud service.

### 9. **Basic Plugin Customization**
   - **Custom Block Page**: Allow users to customize the message or page that blocked users see when their IP has been blocked.
   - **Scheduled Blocking**: Implement a feature to schedule IP blocking and unblocking at specific times or intervals.

---

### Contribution and Feedback

We welcome contributions and feedback from the community! If you have ideas for new features or improvements, please feel free to [open an issue](https://github.com/DoonOnthon/bait-user/issues) or submit a pull request.

Stay tuned for updates as we continue to develop and enhance the Bait-User plugin with these exciting new features!
