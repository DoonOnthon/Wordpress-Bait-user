# Bait-User WordPress Plugin
**Tested versions**
!Versions were tested on empty wordpress installs!
| Version | Supported          |
| ------- | ------------------ |
| 6.5.5   | :white_check_mark: |
| 6.6.1   | :white_check_mark: |
## Description

**Bait-User** is a WordPress plugin designed to enhance your site's security by blocking users based on specific bait accounts. When a user attempts to log in with the bait account, their IP address is captured and blocked automatically. The plugin also provides an option to block IPs at the server level by modifying the `.htaccess` file.

## Features

- Automatically block users by their IP address when they attempt to log in with a bait account.
- View a list of all blocked IP addresses directly from the WordPress admin.
- Optionally update your `.htaccess` file to block IPs at the server level.
- User-friendly settings page to configure the bait account and `.htaccess` options.

## Installation

1. Download the plugin files from this repository.
2. Upload the `bait-user` folder to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Go to **Bait User** in the WordPress admin sidebar to configure the plugin.

## Usage

1. **Select Bait User**: Choose a bait user from the drop-down list on the settings page.
2. **Enable .htaccess Blocking**: Optionally enable automatic updates to the `.htaccess` file for IP blocking.
3. **View Blocked IPs**: Monitor blocked IPs directly from the plugin settings page.

## Screenshots
SOON

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository.
2. **Create a new branch**: Before you make any changes, create a new branch for your feature or bugfix. This keeps the `master` branch clean and ensures that your work can be reviewed without affecting the stable code.
   ```bash
   git checkout -b feature/your-feature-name
3. Commit your changes: `git commit -m 'Add some feature'`.
4. Push to the branch: `git push origin feature/your-feature-name`.
5. Submit a pull request.

## License

This project is licensed under the **[Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 International License](https://creativecommons.org/licenses/by-nc-nd/4.0/)**. 

**You are not allowed to:**
- Use the code for commercial purposes.
- Distribute modified versions of the code.
- Sub-license or sell the code.

**You are allowed to:**
- Use the code for personal, educational, or non-commercial purposes with proper attribution.
- Share the unmodified code.

For more details, see the [LICENSE](LICENSE) file in this repository.

## Support

If you have any questions or need help, feel free to open an issue or contact [Dean](https://github.com/DoonOnthon).

---

**Disclaimer**: Use this plugin at your own risk. Always back up your `.htaccess` file and database before making any changes.

