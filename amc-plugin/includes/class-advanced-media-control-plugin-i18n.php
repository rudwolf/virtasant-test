<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://profiles.wordpress.org/rudwolf/
 * @since      1.0.0
 *
 * @package    Advanced_Media_Control_Plugin
 * @subpackage Advanced_Media_Control_Plugin/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Advanced_Media_Control_Plugin
 * @subpackage Advanced_Media_Control_Plugin/includes
 * @author     Rodolfo Rodrigues <rudwolf@gmail.com>
 */
class Advanced_Media_Control_Plugin_I18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'advanced-media-control-plugin',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
