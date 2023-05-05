/**
 *
 * All of the code for the Admin Front End source
 * should reside in this file.
 *
 * @link       https://profiles.wordpress.org/rudwolf/
 * @since      1.0.0
 *
 * @package    Advanced_Media_Control_Plugin
 * @subpackage Advanced_Media_Control_Plugin/admin
 */

(function ($) {
	'use strict';
	$( document ).on(
		"click",
		".delete-attachment",
		function (e) {
			e.preventDefault();

			let post_id = $( this ).parent().parent().parent().parent().find( ".compat-field-id th label" ).attr( "for" ).replace( /[^0-9]/g, '' );

			$.ajax(
				{
					type: 'POST',
					url: ajaxurl,
					data: {
						action: 'ammp_delete',
						security: ajax_var.nonce,
						post_id: post_id,

					},
					success: function (response) {
						if (response.code == 1) {
							reset_media_library()
							const newURL = location.href.split( "?" )[0];
							window.history.pushState( 'object', document.title, newURL );
							$( ".media-modal" ).parent().hide();
							$( "body" ).removeClass( "modal-open" );
						} else {
							if (response.msg) {
								alert( response.msg );
							}

						}
					}
				}
			);
		}
	);

	function reset_media_library() {
		if (wp.media.frame.library) {
			wp.media.frame.library.props.set( {ignore: (+ new Date())} );
		} else if (wp.media.frame.content.get().collection) {
			wp.media.frame.content.get().collection.props.set( {ignore: (+ new Date())} );
			wp.media.frame.content.get().options.selection.reset();
		}
	}

})( jQuery );
