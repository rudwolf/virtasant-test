<?php
/**
 *
 * @link              https://profiles.wordpress.org/rudwolf/
 * @since             1.0.0
 * @package           Advanced_Media_Control_Plugin
 *
 * @wordpress-plugin
 * Plugin Name:       Advanced Media Control Plugin
 * Plugin URI:        https://#
 * Description:       Plugin to control the use of media files
 * Version:           1.0.0
 * Author:            Rodolfo Rodrigues
 * Author URI:        https://profiles.wordpress.org/rudwolf/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       advanced-media-control-plugin
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined('WPINC') ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('ADVANCED_MEDIA_CONTROL_VERSION', '1.0.0');

/**
 * The library CMB2 activation, including in Plugin,
 */

require_once plugin_dir_path(__FILE__) . '/includes/cmb2/init.php';

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */

require plugin_dir_path(__FILE__) . 'includes/class-advanced-media-control-plugin.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ammp()
{

	$plugin = new Advanced_Media_Control_Plugin();
	$plugin->run();
}
run_ammp();
