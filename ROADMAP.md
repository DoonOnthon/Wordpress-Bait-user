# Bait-User Plugin Roadmap

*Please note: This roadmap is subject to change based on user feedback, development priorities, and evolving needs. It's intended to provide an overview of potential features and improvements for the Bait-User plugin.*

This roadmap outlines the future development plans and potential features for the Bait-User WordPress Plugin. The goal is to continually improve the plugin's functionality, security, and user experience while keeping the feature set practical and user-focused.

## Version 1.2.x | ✅ |

### 1. **IP Whitelisting** | :white_check_mark: |
   - **Whitelist Specific IPs**: Implement a feature that allows administrators to whitelist certain IP addresses, ensuring they are never blocked by the plugin, even if they attempt to log in with the bait user. 

### 2. **IP Range Blocking** | :white_check_mark: |
   - **Block IP Ranges**: Add the ability to block entire IP ranges, providing more comprehensive security against threats originating from known problematic IP blocks. </br>
     
## Version 1.3.x
### 3. **Logging and Reporting**
   - **Login Logs**: Create a logging system that records all login attempts, including successful, failed, and blocked attempts, along with IP addresses, timestamps, and usernames. | ✅ |
   - **Activity Logs**: Create a logging system that records activities, along with IP addresses, timestamps, and usernames.
   - **Export Logs - NOT A PRIORITY**: Implement an option to export logs to CSV or JSON format for further analysis or integration with other security tools.
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

## Version 1.5.x and Beyond **(VERY LIKELY TO CHANGE)**

### 8. **Cloud Integration (Future Consideration)**
   - **Cloud-Based IP Blocking**: Integrate with cloud security services to block known malicious IPs in real-time across multiple sites using the plugin.
   - **Centralized Logging**: Allow centralized logging and management of blocked IPs across multiple WordPress sites through a cloud service.

### 9. **Basic Plugin Customization**
   - **Custom Block Page**: Allow users to customize the message or page that blocked users see when their IP has been blocked.
   - **Scheduled Blocking**: Implement a feature to schedule IP blocking and unblocking at specific times or intervals.

### 10. **Move .htaccess writing to seperate file**
   - **Extract IP Blocking Rules**: Relocate the IP addresses currently being blocked in the .htaccess file to a dedicated configuration file, ensuring a cleaner and more manageable .htaccess structure.

   - **Implement Include Directive**: Update the .htaccess file to include the new configuration file, maintaining the same 

---

### Contribution and Feedback

We welcome contributions and feedback from the community! If you have ideas for new features or improvements, please feel free to [open an issue](https://github.com/DoonOnthon/bait-user/issues) or submit a pull request.

Stay tuned for updates as we continue to develop and enhance the Bait-User plugin with these exciting new features!
