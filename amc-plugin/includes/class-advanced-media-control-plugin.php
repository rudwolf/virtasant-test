<?php
/**
 *
 * The file that defines the core plugin class
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://profiles.wordpress.org/rudwolf/
 * @since      1.0.0
 *
 * @package    Advanced_Media_Control_Plugin
 * @subpackage Advanced_Media_Control_Plugin/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Advanced_Media_Control_Plugin
 * @subpackage Advanced_Media_Control_Plugin/includes
 * @author     Rodolfo Rodrigues <rudwolf@gmail.com>
 */
class Advanced_Media_Control_Plugin {



	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Advanced_Media_Control_Plugin_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'ADVANCED_MEDIA_CONTROL_VERSION' ) ) {
			$this->version = ADVANCED_MEDIA_CONTROL_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'advanced-media-control-plugin';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->load_rest_api();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Advanced_Media_Control_Plugin_Loader. Orchestrates the hooks of the plugin.
	 * - Advanced_Media_Control_Plugin_i18n. Defines internationalization functionality.
	 * - Advanced_Media_Control_Plugin_Admin. Defines all hooks for the admin area.
	 * - Advanced_Media_Control_Plugin_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-advanced-media-control-plugin-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-advanced-media-control-plugin-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-advanced-media-control-plugin-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-advanced-media-control-plugin-public.php';

		$this->loader = new Advanced_Media_Control_Plugin_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Advanced_Media_Control_Plugin_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new Advanced_Media_Control_Plugin_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Advanced_Media_Control_Plugin_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'cmb2_admin_init', $plugin_admin, 'ammp_edit_term_fields' );
		$this->loader->add_action( 'delete_attachment', $plugin_admin, 'ammp_disable_media_deletion', 10 );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'ammp_show_notice' );
		$this->loader->add_filter( 'manage_media_columns', $plugin_admin, 'ammp_custom_media_columns', 10 );
		$this->loader->add_action( 'manage_media_custom_column', $plugin_admin, 'ammp_custom_media_columns_content', 10, 2 );
		$this->loader->add_action( 'wp_ajax_ammp_delete', $plugin_admin, 'ammp_delete_handler' );
		$this->loader->add_filter( 'attachment_fields_to_edit', $plugin_admin, 'ammp_add_custom_attachment_action_field', 10, 2 );
		$this->loader->add_filter( 'cmb2_meta_box_url', $plugin_admin, 'update_cmb2_meta_box_url' );

	}


	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new Advanced_Media_Control_Plugin_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute restapi hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function load_rest_api() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-advanced-media-control-plugin-rest.php';
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 * @since     1.0.0
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    Advanced_Media_Control_Plugin_Loader    Orchestrates the hooks of the plugin.
	 * @since     1.0.0
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 * @since     1.0.0
	 */
	public function get_version() {
		return $this->version;
	}

}
