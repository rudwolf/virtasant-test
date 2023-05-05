<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://profiles.wordpress.org/rudwolf/
 * @since      1.0.0
 *
 * @package    Advanced_Media_Control_Plugin
 * @subpackage Advanced_Media_Control_Plugin/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Advanced_Media_Control_Plugin
 * @subpackage Advanced_Media_Control_Plugin/admin
 * @author     Rodolfo Rodrigues <rudwolf@gmail.com>
 */
class Advanced_Media_Control_Plugin_Admin
{



	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;


	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $error_message The default error of images.
	 */
	public $error_message;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 * @since    1.0.0
	 */
	public function __construct($plugin_name, $version)
	{
		$this->plugin_name   = $plugin_name;
		$this->version       = $version;
		$this->error_message = __("Removal Forbidden: Image is linked to post(s) or term(s), please remove the image from the post(s) or term(s) where it is being used to continue. ", 'advanced-media-control-plugin');
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Advanced_Media_Control_Plugin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Advanced_Media_Control_Plugin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/advanced-media-control-plugin-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Advanced_Media_Control_Plugin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Advanced_Media_Control_Plugin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/advanced-media-control-plugin-admin.js', array('jquery'), $this->version, false);
		wp_localize_script(
			$this->plugin_name,
			'ajax_var',
			array(
				'url'   => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('ajax-nonce'),
			)
		);
	}

	public function update_cmb2_meta_box_url($url)
	{
		return site_url('/wp-content/plugins/amc-plugin/includes/cmb2');
	}

	/**
	 * Adding CMB2 Term Image hook attached (cmb2_admin_init).
	 *
	 * @since    1.0.0
	 */
	public function ammp_edit_term_fields()
	{
		$prefix = 'ammp_term_';

		$cmb = new_cmb2_box(array(
			'id'           => $prefix . 'metabox',
			'title'        => esc_html__('Term Image', 'cmb2'),
			'object_types' => array('term'), // Taxonomy term
			'taxonomies'   => array('category'),
			'show_on'      => array(
				'key'   => 'taxonomy',
				'value' => 'category', // Change this to your taxonomy name
			),
			'context'      => 'side', // Show on side of term edit screen
			'priority'     => 'default',
		));

		$cmb->add_field(array(
			'name'             => esc_html__('Image', 'cmb2'),
			'desc'             => esc_html__('Upload an image or enter a URL.', 'cmb2'),
			'id'               => $prefix . 'image',
			'type'             => 'file',
			'preview_size'     => 'medium',
			'query_args'       => array('type' => 'image'),
			'text'             => array(
				'add_upload_file_text' => esc_html__('Add Image', 'cmb2'),
			),
			'attributes'       => array(
				'required' => true, // Set to true if you want the field to be required
			),
		));
	}

	public function ammp_show_notice()
	{
		if (isset($_GET['ammp_notice']) && 1 == $_GET['ammp_notice']) {
			$base_error = $this->error_message;
			ob_start(); ?>
			<div class="notice notice-error amc-error-notice">
				<p><?php echo $base_error; ?></p>
				<ul>
					<?php if (isset($_GET['ammp_notice_posts'])) : ?>
						<li>It is being used in the content of a the following post(s): <?php echo $_GET['ammp_notice_posts']; ?></li>
					<?php endif; ?>
					<?php if (isset($_GET['ammp_notice_featured'])) : ?>
						<li>It is being used as a featured image in the following post(s): <?php echo $_GET['ammp_notice_featured']; ?> </li>
					<?php endif; ?>
					<?php if (isset($_GET['ammp_notice_terms'])) : ?>
						<li>It is being used as a Term Image in the following term(s): <?php echo $_GET['ammp_notice_terms']; ?> </li>
					<?php endif; ?>
				</ul>
			</div>
			<?php
			echo ob_get_clean();
		}
	}

	public function my_cmb2_enqueue_scripts()
	{
		// Check if we are on the term edit or new term page for any registered taxonomy
		$screen = get_current_screen();
		$taxonomies = get_taxonomies();
		if (in_array($screen->id, $taxonomies)) {
			// Initialize CMB2
			echo '<script>jQuery(document).ready(function($){window.CMB2.init();});</script>';
		}
	}


	/**
	 * Function ammp_disable_media_deletion Disable media deletion hook attached (delete_attachment) .
	 *
	 * @param string $post_ID id of the attachment.
	 * @since 1.0.0
	 */
	public function ammp_disable_media_deletion($post_ID)
	{
		$msg = $this->ammp_get_response_message($post_ID, 1, true);
		if (!empty($msg)) {
			$redirect_url = admin_url('upload.php');
			$queryArgs = array(
				'ammp_notice' => '1',
			);
			foreach ($msg as $type => $ids) {
				if (!empty($ids)) {
					$queryArgs['ammp_notice_' . $type] = implode(',', $ids);
				}
			}
			$redirect_url = add_query_arg($queryArgs, $redirect_url);
			wp_redirect($redirect_url);
			exit;
		}
	}


	/**
	 * Function ammp_prevent_featured_image_deletion Disable media deletion hook attached (delete_attachment) .
	 *
	 * @param string $post_ID id of the attachment.
	 * @param int    $ajax_res show where ajax request or not.
	 * @since 1.0.0
	 */
	public function ammp_prevent_featured_image_deletion($post_ID, $ajax_res = 0)
	{
		$featured_image_query = new WP_Query(
			array(
				'post_type'  => 'post',
				'meta_key'   => '_thumbnail_id', // phpcs:ignore
				'meta_value' => $post_ID, // phpcs:ignore
			)
		);
		$post_url = array();
		if ($featured_image_query->have_posts()) {
			while ($featured_image_query->have_posts()) {
				$featured_image_query->the_post();
				$id = get_the_ID();
				if (1 === $ajax_res) {
					$post_url[] = $id;
				} else {
					$post_url[$id] = add_query_arg(
						array(
							'post'   => $id,
							'action' => 'edit',
						),
						admin_url('post.php')
					);
				}
			}
			$comma_separated = array();
			if (!empty($post_url)) {
				foreach ($post_url as $key => $single) {
					$single            = esc_url($single);
					$key               = esc_html($key);
					$comma_separated[] = "<a href='$single'>$key</a>";
				}
			}
			if (1 === $ajax_res) {
				return $post_url;
			} else {
				return implode(',', $comma_separated);
			}
		}
	}

	/**
	 * Disable media deletion hook attached (delete_attachment).
	 *
	 * @param string $post_ID id of the attachment.
	 * @param int    $ajax_res show where ajax request or not.
	 * @since 1.0.0
	 */
	public function ammp_prevent_content_image_deletion($post_ID, $ajax_res = 0)
	{
		$post_info = get_post($post_ID);
		if (!empty($post_info)) {
			$url      = $post_info->guid;
			$posts    = get_posts(
				array(
					'post_type'   => 'post',
					'post_status' => 'publish', // not using 'any' (trash not included).
					'numberposts' => -1, // All post.
				)
			);
			$post_url = array();
			foreach ($posts as $post) {
				$content = $post->post_content;
				if (strpos($content, $url) !== false) { // Below PHP 8.0 supported.
					$id = $post->ID;
					if (1 === $ajax_res) {
						$post_url[] = $id;
					} else {
						$post_url[$id] = add_query_arg(
							array(
								'post'   => $id,
								'action' => 'edit',
							),
							admin_url('post.php')
						);
					}
				}
			}
			return $this->generate_url_links($post_url, $ajax_res);
		} else {
			return '';
		}
	}

	/**
	 * Disable media deletion hook attached (delete_attachment) .
	 *
	 * @param string $post_ID id of the attachment.
	 * @param int    $ajax_res show where ajax request or not.
	 * @since 1.0.0
	 */
	public function ammp_prevent_term_image_deletion($post_ID, $ajax_res = 0)
	{
		$args     = array(
			'taxonomy'   => 'category',
			'hide_empty' => false,
			'meta_key'   => 'ammp_term_image_id',
			'meta_value' => $post_ID,
		);
		$terms    = get_terms($args);
		$post_url = array();
		foreach ($terms as $term) {
			$id = $term->term_id;
			if (1 === $ajax_res) {
				$post_url[] = $id;
			} else {
				$post_url[$id] = get_edit_term_link($id);
			}
		}
		return $this->generate_url_links($post_url, $ajax_res);
	}

	/**
	 * Add media columns on libarary page.
	 *
	 * @param array $columns all the media library columns.
	 * @return array $columns
	 * @since    1.0.0
	 */
	public function ammp_custom_media_columns($columns)
	{
		unset($columns['cb']);
		unset($columns['title']);
		unset($columns['author']);
		unset($columns['comments']);
		unset($columns['parent']);
		unset($columns['date']);

		$columns['cb']               = __('cb', 'advanced-media-control-plugin');
		$columns['title']            = __('Title', 'advanced-media-control-plugin');
		$columns['author']           = __('Author', 'advanced-media-control-plugin');
		$columns['parent']           = __('Uploaded To', 'advanced-media-control-plugin');
		$columns['comments']         = __('<i class="fa comment-grey-bubble" aria-hidden="true"></i>', 'advanced-media-control-plugin');
		$columns['attached_objects'] = __('Attached Objects', 'advanced-media-control-plugin');
		$columns['date']             = __('Date', 'advanced-media-control-plugin');
		return $columns;
	}


	/**
	 * Add media columns.
	 *
	 * @param array $column_name current column of the media library.
	 * @param int   $attachment_id of the post.
	 * @return void $columns
	 * @since    1.0.0
	 */
	public function ammp_custom_media_columns_content($column_name, $attachment_id)
	{
		if ('attached_objects' === $column_name) {

			$post_id = intval($attachment_id);
			$result  = $this->ammp_linked_articles($post_id); // $result is already escaped during creation
			echo implode(', ', $result); // phpcs:ignore
		}
	}


	/**
	 * Add media columns controls.
	 *
	 * @param array  $form_fields current form fields column of the media library.
	 * @param object $post current post.
	 * @return array $form_fields
	 * @since    1.0.0
	 */
	public function ammp_add_custom_attachment_action_field($form_fields, $post)
	{
		$post_id           = $post->ID;
		$result            = $this->ammp_linked_articles($post_id);
		$string            = implode(', ', $result);
		$form_fields['id'] = array(
			'label' => __('Linked Articles', 'advanced-media-control-plugin'),
			'input' => 'html',
			'html'  => ' <label><span>' . $string . '</span></label>',
		);

		return $form_fields;
	}

	/**
	 * Add media columns Content.
	 *
	 * @return  string|void handler
	 * @since    1.0.0
	 */
	public function ammp_delete_handler()
	{
		check_ajax_referer('ajax-nonce', 'security');
		if (isset($_POST['post_id'])) {

			$post_ID = intval($_POST['post_id']);

			$msg = $this->ammp_get_response_message($post_ID, 1);

			if (!empty($msg)) {
				$result = array(
					'code' => 0,
					'msg'  => $this->error_message . $msg,
				);
				wp_send_json($result);
			}
		}
	}

	/**
	 * Disable media deletion hook attached (delete_attachment) .
	 *
	 * @param int $post_ID for fetching post data.
	 * @return array $result all the ids.
	 * @since 1.0.0
	 */
	public function ammp_linked_articles($post_ID)
	{
		$final_array    = array();
		$featured_image = $this->ammp_prevent_featured_image_deletion($post_ID, 0);
		$final_array[]  = $featured_image;
		$content_image  = $this->ammp_prevent_content_image_deletion($post_ID, 0);
		$final_array[]  = $content_image;
		$term_image     = $this->ammp_prevent_term_image_deletion($post_ID, 0);
		$final_array[]  = $term_image;
		$remove_empty   = array_filter($final_array);
		$comma_sep      = implode(',', $remove_empty);
		$merge_all      = explode(',', $comma_sep);
		$result         = array();
		foreach ($merge_all as $key => $value) {
			if (!in_array($value, $result, true)) {
				$result[$key] = $value;
			}
		}
		return $result;
	}

	/**
	 * Function ammp_get_response_message Disable media deletion hook attached (delete_attachment) .
	 *
	 * @param string $post_ID id of the attachment.
	 * @param int    $ajax_res show where ajax request or not.
	 * @since 1.0.0
	 */
	public function ammp_get_response_message($post_ID, $ajax_res = 0, $ids_only = false)
	{
		if ($ids_only) {
			$found = [
				'featured' => [],
				'posts'    => [],
				'terms'    => [],
			];
			$ajax_res = 1;
		}
		$msg            = '';
		$featured_image = $this->ammp_prevent_featured_image_deletion($post_ID, $ajax_res, $ids_only);
		if (!empty($featured_image)) {
			if ($ids_only) {
				$found['featured'] = $featured_image;
			}
			if (1 === $ajax_res) {
				$featured_image = implode(',', $featured_image);
			}
			$msg .= __('It is being used as a featured image in the following post(s): ', 'advanced-media-control-plugin') . $featured_image . ' ';
		}
		$content_image = $this->ammp_prevent_content_image_deletion($post_ID, $ajax_res, $ids_only);

		if (!empty($content_image)) {
			if ($ids_only) {
				$found['posts'] = $content_image;
			}
			if (1 === $ajax_res) {
				$content_image = implode(',', $content_image);
			}
			$msg .= __('It is being used in the content of a the following post(s): ', 'advanced-media-control-plugin') . $content_image . ' ';
		}

		$term_image = $this->ammp_prevent_term_image_deletion($post_ID, $ajax_res, $ids_only);

		if (!empty($term_image)) {
			if ($ids_only) {
				$found['terms'] = $term_image;
			}
			if (1 === $ajax_res) {
				$term_image = implode(',', $term_image);
			}
			$msg .= __('It is being used as a Term Image in the following term(s): ', 'advanced-media-control-plugin') . $term_image . ' ';
		}
		if (true == $ids_only) {
			return $found;
		} else {
			return $msg;
		}
	}

	/**
	 * Function generate_url_links escaped url links for admin panel (delete_attachment) .
	 *
	 * @param array $post_url containing all links.
	 * @param int   $ajax_res if its ajax request or not.
	 * @return array|string|void
	 */
	public function generate_url_links(array $post_url, int $ajax_res)
	{
		$comma_separated = array();
		if (!empty($post_url)) {
			foreach ($post_url as $key => $single) {
				$single            = esc_url($single);
				$key               = esc_html($key);
				$comma_separated[] = "<a href='$single'>$key</a>";
			}
			if (1 === $ajax_res) {
				return $post_url;
			} else {
				return implode(',', $comma_separated);
			}
		}
	}
}
