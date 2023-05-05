<?php
/**
 * Defines REST API endpoints
 *
 * @link       https://profiles.wordpress.org/rudwolf/
 * @since      1.0.0
 *
 * @package    Advanced_Media_Control_Plugin
 * @subpackage Advanced_Media_Control_Plugin/includes
 */

/**
 * This class is responsible to implement the rest API endpoints
 *
 * @since      1.0.0
 * @package    Advanced_Media_Control_Plugin_Rest
 * @subpackage Advanced_Media_Control_Plugin/includes
 * @author     Rodolfo Rodrigues <rudwolf@gmail.com>
 */
class Advanced_Media_Control_Plugin_Rest
{

	/**
	 * Maintains the data thorough out the class
	 *
	 * @private  ammp storing Admin class
	 * @var  $ammp
	 * @since    1.0.0
	 * */
	private $ammp;

	/**
	 * Maintains the API namespace thorough out the class
	 *
	 * @private  ammp storing Admin class
	 * @var  $ammp
	 * @since    1.0.0
	 * */
	private $namespace;

	/**
	 * Define the core functionality of the class.
	 *
	 * Get the plugin name and the plugin version that can be used throughout the plugin.
	 * set namespace etc.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		$this->namespace  = 'assignment/v1';
		$params           = new Advanced_Media_Control_Plugin();
		$ammp       = new Advanced_Media_Control_Plugin_Admin($params->get_plugin_name(), $params->get_version());
		$this->ammp = $ammp;
		add_action('rest_api_init', array($this, 'register_routes'));
	}


	/**
	 * Register rest our routes.
	 */
	public function register_routes()
	{
		// fetch image rest method.
		register_rest_route(
			$this->namespace,
			'/getImage',
			array(
				array(
					'methods'             => 'Post',
					'callback'            => array($this, 'get_image'),
					'permission_callback' => '__return_true',
				),
			)
		);
		// delete image rest method.
		register_rest_route(
			$this->namespace,
			'/deleteImage',
			array(
				'methods' => 'DELETE',
				'callback' => array($this, 'delete_image'),
				'args' => array(
					'post_id' => array(
						'required' => true,
						'type' => 'integer',
					),
				),
				'permission_callback' => array($this, 'check_user_permission'),
			)
		);
	}

	public function check_user_permission()
	{
		return is_user_logged_in() && current_user_can('delete_posts');
	}


	/**
	 * This function used to get image detail by id in rest api
	 *
	 * @param array $request got in json from in rest post method.
	 * @return array response
	 * @since    1.0.0
	 */
	public function get_image($request)
	{
		$request_body   = $request->get_body();
		$sting_to_array = json_decode($request_body);
		$image_id       = intval($sting_to_array->post_id);
		$is_attachment  = wp_get_attachment_image_src($image_id);
		if (!empty($is_attachment)) {
			$post_info      = get_post($image_id);
			$post_meta_info = wp_get_attachment_metadata($image_id);
			$image_alt      = get_post_meta($image_id, '_wp_attachment_image_alt', true);
			$featured       = $this->ammp->ammp_prevent_featured_image_deletion($image_id, 1);
			$content        = $this->ammp->ammp_prevent_content_image_deletion($image_id, 1);
			$term           = $this->ammp->ammp_prevent_term_image_deletion($image_id, 1);

			if (empty($featured)) {
				$featured = array();
			}
			if (empty($content)) {
				$content = array();
			}
			if (empty($term)) {
				$term = array();
			}
			$data_array = array(
				'id'               => $post_info->ID,
				'date'             => $post_info->post_date,
				'date_gmt'         => $post_info->post_date_gmt,
				'slug'             => $post_info->guid,
				'type'             => $post_info->post_mime_type,
				'link'             => $post_info->guid,
				'alt_text'         => $image_alt,
				'attached_objects' => array(
					'post' => array(
						'featured' => implode(',', $featured),
						'content'  => implode(',', $content),
					),
					'term' => implode(',', $term),
				),
				'meta_data'        => array(
					'meta_obj' => $post_meta_info,
					'post_obj' => $post_info,
				),
			);
		} else {
			$data_array = array(
				'msg' => null,
			);
		}

		$response['code'] = 200;
		$response['data'] = $data_array;
		return $response;
	}

	/**
	 * This function used to delete image by id in rest api
	 *
	 * @param array $request got in json from in rest Delete method.
	 * @return array response
	 * @since    1.0.0
	 */
	public function delete_image(WP_REST_Request $request)
	{
		$image_id       = $request->get_param('post_id');
		$is_attachment  = wp_get_attachment_image_src($image_id);
		$data_array     = array(
			'msg' => null,
		);
		if (!empty($is_attachment)) {
			$msg = $this->ammp->ammp_get_response_message($image_id, 1);
			if (!empty($msg)) {
				$data_array = array(
					'msg' => $this->ammp->error_message . $msg,
				);
			} else {
				// Delete attachment files (image sizes) from the server.
				$meta = wp_get_attachment_metadata($image_id);
				$upload_dir = wp_get_upload_dir();
				if (isset($meta['file'])) {
					$file = $upload_dir['basedir'] . '/' . dirname($meta['file']);
					foreach ($meta['sizes'] as $size) {
						$path = $file . '/' . $size['file'];
						@unlink($path);
					}
					@unlink($upload_dir['basedir'] . '/' . $meta['file']);
				}

				// Delete the attachment post and metadata from the database.
				global $wpdb;
				$deleted_post = $wpdb->delete($wpdb->posts, array('ID' => $image_id, 'post_type' => 'attachment'));
				$deleted_metadata = $wpdb->delete($wpdb->postmeta, array('post_id' => $image_id));

				if ($deleted_post && $deleted_metadata) {
					// The post and metadata were deleted successfully.
					$success_msg = __('Deletion Success {id} ', 'advanced-media-control-plugin');
					$data_array  = array(
						'msg'         => $success_msg . $image_id,
						'deleted_obj' => $deleted_post,
					);
				} else {
					// The post or metadata could not be deleted.
					$data_array = array(
						'msg' => 'Deletion failed for attachment ID ' . $image_id,
					);
				}
			}
		}
		$response['code'] = 200;
		$response['data'] = $data_array;

		$wp_rest_response = new WP_REST_Response($response['data'], $response['code']);
		return $wp_rest_response;
	}
}

$activate = new Advanced_Media_Control_Plugin_Rest();
