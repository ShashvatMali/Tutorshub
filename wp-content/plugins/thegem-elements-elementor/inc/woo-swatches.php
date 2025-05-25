<?php

function thegem_add_product_attribute_color( $attrs ) {
	return array_merge(
		$attrs,
		array(
			'color' => __( 'Color Swatches', 'thegem' ),
			'image' => __( 'Image Swatches', 'thegem' ),
			'label' => __( 'Label Swatches', 'thegem' ),
		)
	);
}
add_filter( 'product_attributes_type_selector', 'thegem_add_product_attribute_color', 10, 1 );

function thegem_woocommerce_after_add_attribute_fields() {
?>
<div class="form-field" style="display: none;">
	<label for="thegem_attribute_image_tooltip_type"><?php esc_html_e( 'Tooltip type', 'thegem' ); ?></label>
	<select name="thegem_attribute_image_tooltip_type" id="thegem_attribute_image_tooltip_type">
		<option value="image"><?php esc_html_e( 'Image', 'thegem' ); ?></option>
		<option value="text"><?php esc_html_e( 'Text', 'thegem' ); ?></option>
		<option value="image-text"><?php esc_html_e( 'Image + Text', 'thegem' ); ?></option>
	</select>
	<script type="text/javascript">
	jQuery( document ).on( 'change', '#attribute_type', function( event ) {
		if(jQuery(this).val() === 'image') {
			jQuery('#thegem_attribute_image_tooltip_type').closest('.form-field').show();
		} else {
			jQuery('#thegem_attribute_image_tooltip_type').closest('.form-field').hide();
		}
	});
	jQuery('#attribute_type').trigger('change');
	</script>
</div>
<?php
}
add_action( 'woocommerce_after_add_attribute_fields', 'thegem_woocommerce_after_add_attribute_fields');

function thegem_woocommerce_after_edit_attribute_fields() {
	global $wpdb;
	$edit = isset( $_GET['edit'] ) ? absint( $_GET['edit'] ) : 0;

	$attribute_to_edit = $wpdb->get_row(
		$wpdb->prepare(
			"
			SELECT attribute_type, attribute_label, attribute_name, attribute_orderby, attribute_public
			FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_id = %d
			",
			$edit
		)
	);
	$att_name = $attribute_to_edit->attribute_name;

	$tooltip_type = get_option('thegem_attribute_'.$att_name.'_image_tooltip_type');
?>
<tr class="form-field" style="display: none;">
	<th scope="row" valign="top">
		<label for="thegem_attribute_image_tooltip_type"><?php esc_html_e( 'Tooltip type', 'thegem' ); ?></label>
	</th>
	<td>
		<select name="thegem_attribute_image_tooltip_type" id="thegem_attribute_image_tooltip_type">
			<option value="image" <?php selected( $tooltip_type, 'image' ); ?>><?php esc_html_e( 'Image', 'thegem' ); ?></option>
			<option value="text" <?php selected( $tooltip_type, 'text' ); ?>><?php esc_html_e( 'Text', 'thegem' ); ?></option>
			<option value="image-text" <?php selected( $tooltip_type, 'image-text' ); ?>><?php esc_html_e( 'Image + Text', 'thegem' ); ?></option>
		</select>
		<script type="text/javascript">
		jQuery( document ).on( 'change', '#attribute_type', function( event ) {
			if(jQuery(this).val() === 'image') {
				jQuery('#thegem_attribute_image_tooltip_type').closest('.form-field').show();
			} else {
				jQuery('#thegem_attribute_image_tooltip_type').closest('.form-field').hide();
			}
		});
		jQuery('#attribute_type').trigger('change');
		</script>
	</td>
</tr>
<?php
}
add_action( 'woocommerce_after_edit_attribute_fields', 'thegem_woocommerce_after_edit_attribute_fields');

function thegem_woocommerce_attribute_image_save($id, $data, $old_slug = false) {
	if(!empty($old_slug)) {
		delete_option('thegem_attribute_'.$old_slug.'_image_tooltip_type');
	}
	if($data['attribute_type'] === 'image') {
		$type = isset( $_POST['thegem_attribute_image_tooltip_type'] ) && in_array($_POST['thegem_attribute_image_tooltip_type'], array('text', 'image', 'image-text')) ? $_POST['thegem_attribute_image_tooltip_type'] : 'image';
		update_option('thegem_attribute_'.$data['attribute_name'].'_image_tooltip_type', $type);
	}
}
add_action( 'woocommerce_attribute_added', 'thegem_woocommerce_attribute_image_save', 10, 2);
add_action( 'woocommerce_attribute_updated', 'thegem_woocommerce_attribute_image_save', 10, 3);

function thegem_woocommerce_attribute_image_delete($id, $name, $taxonomy) {
	delete_option('thegem_attribute_'.$name.'_image_tooltip_type');
}
add_action( 'woocommerce_attribute_deleted', 'thegem_woocommerce_attribute_image_delete', 10, 3 );

function thegem_init_swatches( $attrs ) {
	if ( defined( 'WC_PLUGIN_FILE' ) && $attribute_taxonomies = wc_get_attribute_taxonomies() ) {
		$added_action = false;
		foreach ( $attribute_taxonomies as $tax ) {
			if ( 'color' === $tax->attribute_type || 'image' === $tax->attribute_type || 'label' === $tax->attribute_type ) {
				add_action( wc_attribute_taxonomy_name( $tax->attribute_name ) . '_add_form_fields', 'thegem_add_product_attribute_' . $tax->attribute_type . '_fields', 100, 1 );
				add_action( wc_attribute_taxonomy_name( $tax->attribute_name ) . '_edit_form_fields', 'thegem_edit_product_attribute_' . $tax->attribute_type . '_fields', 100, 2 );

				if ( ! $added_action ) {
					add_action( 'edit_term', 'thegem_save_product_extra_attribute_values', 100, 3 );
					add_action( 'delete_term', 'thegem_delete_product_extra_attribute_values', 10, 5 );
					add_action( 'created_term', 'thegem_save_product_extra_attribute_values', 100, 3 );
					$added_action = true;
				}
			}
		}
	}
}
add_action( 'init', 'thegem_init_swatches');

function thegem_save_product_extra_attribute_values( $term_id, $tt_id, $taxonomy ) {
	if ( strpos( $taxonomy, 'pa_' ) === false ) {
		return;
	}
	if ( $attribute_taxonomies = wc_get_attribute_taxonomies() ) {
		foreach ( $attribute_taxonomies as $tax ) {
			if ( 'color' === $tax->attribute_type && $taxonomy === wc_attribute_taxonomy_name( $tax->attribute_name )) {
				update_term_meta( $term_id, 'thegem_color_display_type', $_POST[ 'thegem_color_display_type' ]);
				update_term_meta( $term_id, 'thegem_color', $_POST[ 'thegem_color' ]);
				update_term_meta( $term_id, 'thegem_color_image', $_POST[ 'thegem_color_image' ]);
				update_term_meta( $term_id, 'thegem_color_dual', !empty($_POST[ 'thegem_color_dual' ]));
				update_term_meta( $term_id, 'thegem_color_2', $_POST[ 'thegem_color_2' ]);
				update_term_meta( $term_id, 'thegem_swatches_tooltip_text', $_POST[ 'thegem_swatches_tooltip_text' ]);
			}
			if ( 'image' === $tax->attribute_type && $taxonomy === wc_attribute_taxonomy_name( $tax->attribute_name )) {
				update_term_meta( $term_id, 'thegem_image', $_POST[ 'thegem_image' ]);
				update_term_meta( $term_id, 'thegem_swatches_tooltip_text', $_POST[ 'thegem_swatches_tooltip_text' ]);
			}
			if ( 'label' === $tax->attribute_type && $taxonomy === wc_attribute_taxonomy_name( $tax->attribute_name )) {
				update_term_meta( $term_id, 'thegem_label', $_POST[ 'thegem_label' ]);
				update_term_meta( $term_id, 'thegem_swatches_tooltip_text', $_POST[ 'thegem_swatches_tooltip_text' ]);
			}
		}
	}
}
function thegem_delete_product_extra_attribute_values( $term_id, $tt_id, $taxonomy, $deleted_term, $object_ids ) {
	if ( strpos( $taxonomy, 'pa_' ) === false ) {
		return;
	}
	if ( $attribute_taxonomies = wc_get_attribute_taxonomies() ) {
		foreach ( $attribute_taxonomies as $tax ) {
			if ( 'color' === $tax->attribute_type && $taxonomy === wc_attribute_taxonomy_name( $tax->attribute_name ) ) {
				delete_term_meta( $term_id, 'thegem_color_display_type' );
				delete_term_meta( $term_id, 'thegem_color' );
				delete_term_meta( $term_id, 'thegem_color_image' );
				delete_term_meta( $term_id, 'thegem_color_dual' );
				delete_term_meta( $term_id, 'thegem_color_2' );
				delete_term_meta( $term_id, 'thegem_swatches_tooltip_text' );
			}
			if ( 'image' === $tax->attribute_type && $taxonomy === wc_attribute_taxonomy_name( $tax->attribute_name ) ) {
				delete_term_meta( $term_id, 'thegem_image' );
				delete_term_meta( $term_id, 'thegem_swatches_tooltip_text' );
			}
			if ( 'label' === $tax->attribute_type && $taxonomy === wc_attribute_taxonomy_name( $tax->attribute_name ) ) {
				delete_term_meta( $term_id, 'thegem_label' );
				delete_term_meta( $term_id, 'thegem_swatches_tooltip_text' );
			}
		}
	}
}

function thegem_get_product_attribute_color_field() {
	return array(
		'name'  => 'thegem_color',
		'title' => __( 'Color', 'thegem' ),
	);
}
function thegem_get_product_attribute_image_field() {
	return array(
		'name'  => 'thegem_image',
		'title' => __( 'Image', 'thegem' ),
	);
}
function thegem_get_product_attribute_label_field() {
	return array(
		'name'  => 'thegem_label',
		'title' => __( 'Label', 'thegem' ),
		'desc'  => __( 'Input short label to be displayed instead of title such as "XS".', 'thegem' ),
	);
}

function thegem_add_product_attribute_color_fields( $taxonomy ) {
	wp_enqueue_script('color-picker');
	wp_enqueue_style('color-picker');
?>
<div class="form-field thegem-term-color-display-type-wrap">
	<label for="thegem_color_display_type"><?php esc_html_e( 'Display type', 'thegem' ); ?></label>
	<?php thegem_print_select_input(array('color' => __('Color', 'thegem'), 'image' => __('Image', 'thegem')), 'color', 'thegem_color_display_type', 'thegem_color_display_type'); ?>
</div>
<div class="form-field thegem-term-color-wrap thegem-hide-on-image">
	<label for="thegem_color"><?php esc_html_e( 'Color', 'thegem' ); ?></label>
	<input type="text" id="thegem_color" name="thegem_color" value="" class="color-select" style="width: auto;" />
</div>
<div class="form-field thegem-term-color-dual-wrap thegem-hide-on-image">
	<label for="thegem_color_dual"><?php esc_html_e( 'Is Dual Color', 'thegem' ); ?></label>
	<input type="checkbox" id="thegem_color_dual" name="thegem_color_dual" value="1" />
</div>
<div class="form-field thegem-term-color-2-wrap thegem-hide-on-image">
	<label for="thegem_color_2"><?php esc_html_e( 'Secondary Color', 'thegem' ); ?></label>
	<input type="text" id="thegem_color_2" name="thegem_color_2" value="" class="color-select" style="width: auto;" />
</div>
<table class="form-field thegem-hide-on-color">
	<tr class="form-field">
		<th scope="row" valign="top"><label for="thegem_color_image"><?php esc_html_e( 'Image', 'thegem' ); ?></label></th>
		<td class="thegem-meta-color-image">
			<div id="thegem_attribute_color_image" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" width="60px" height="60px" /></div>
			<div>
				<button type="button" class="upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'woocommerce' ); ?></button>
				<button type="button" class="remove_image_button button"><?php esc_html_e( 'Remove image', 'woocommerce' ); ?></button>
			</div>
			<input type="hidden" id="thegem_color_image" name="thegem_color_image" value="" />
			<script type="text/javascript">

				// Only show the "remove image" button when needed
				if ( ! jQuery( '#thegem_color_image' ).val() ) {
					jQuery( '.remove_image_button' ).hide();
				}

				// Uploading files
				var file_frame;

				jQuery( document ).on( 'click', '.upload_image_button', function( event ) {

					event.preventDefault();

					// If the media frame already exists, reopen it.
					if ( file_frame ) {
						file_frame.open();
						return;
					}

					// Create the media frame.
					file_frame = wp.media.frames.downloadable_file = wp.media({
						title: '<?php esc_html_e( 'Choose an image', 'woocommerce' ); ?>',
						button: {
							text: '<?php esc_html_e( 'Use image', 'woocommerce' ); ?>'
						},
						multiple: false
					});

					// When an image is selected, run a callback.
					file_frame.on( 'select', function() {
						var attachment           = file_frame.state().get( 'selection' ).first().toJSON();
						var attachment_thumbnail = attachment.sizes.thumbnail || attachment.sizes.full;

						jQuery( '#thegem_color_image' ).val( attachment.id );
						jQuery( '#thegem_attribute_color_image' ).find( 'img' ).attr( 'src', attachment_thumbnail.url );
						jQuery( '.remove_image_button' ).show();
					});

					// Finally, open the modal.
					file_frame.open();
				});

				jQuery( document ).on( 'click', '.remove_image_button', function() {
					jQuery( '#thegem_attribute_color_image' ).find( 'img' ).attr( 'src', '<?php echo esc_js( wc_placeholder_img_src() ); ?>' );
					jQuery( '#thegem_color_image' ).val( '' );
					jQuery( '.remove_image_button' ).hide();
					return false;
				});

				jQuery( document ).ajaxComplete( function( event, request, options ) {
					if ( request && 4 === request.readyState && 200 === request.status
						&& options.data && 0 <= options.data.indexOf( 'action=add-tag' ) ) {

						var res = wpAjax.parseAjaxResponse( request.responseXML, 'ajax-response' );
						if ( ! res || res.errors ) {
							return;
						}
						// Clear Thumbnail fields on submit
						jQuery( '#thegem_attribute_color_image' ).find( 'img' ).attr( 'src', '<?php echo esc_js( wc_placeholder_img_src() ); ?>' );
						jQuery( '#thegem_color_image' ).val( '' );
						jQuery( '.remove_image_button' ).hide();
						return;
					}
				} );

			</script>
		</td>
	</tr>
</table>
<div class="form-field">
	<label for="thegem_swatches_tooltip_text"><?php esc_html_e( 'Tooltip Text', 'thegem' ); ?></label>
	<input type="text" id="thegem_swatches_tooltip_text" name="thegem_swatches_tooltip_text" value="" size="50%" class="text" />
</div>
<script type="text/javascript">
jQuery( document ).on( 'change', '#thegem_color_display_type', function( event ) {
	jQuery('.thegem-hide-on-image, .thegem-hide-on-color').show();
	jQuery('.thegem-hide-on-' + jQuery(this).val()).hide();
	jQuery('#thegem_color_dual').trigger('change');
});
jQuery('#thegem_color_display_type').trigger('change');
jQuery( document ).on( 'change', '#thegem_color_dual', function( event ) {
	if(jQuery(this).is(':checked')) {
		jQuery('.thegem-term-color-2-wrap').show();
	} else {
		jQuery('.thegem-term-color-2-wrap').hide();
	}
});
jQuery('#thegem_color_dual').trigger('change');
</script>
<?php
}

function thegem_edit_product_attribute_color_fields( $tag, $taxonomy ) {
	wp_enqueue_script('color-picker');
	wp_enqueue_style('color-picker');
	$display_type = get_term_meta( $tag->term_id, 'thegem_color_display_type', true );
	$value = get_term_meta( $tag->term_id, 'thegem_color', true );
	$color_dual = get_term_meta( $tag->term_id, 'thegem_color_dual', true );
	$color_2 = get_term_meta( $tag->term_id, 'thegem_color_2', true );
	$color_image = get_term_meta( $tag->term_id, 'thegem_color_image', true );
	$color_image_src = wc_placeholder_img_src();
	if ( $color_image ) {
		$color_image_src = wp_get_attachment_thumb_url( $color_image );
	}
	$tooltip_text = get_term_meta( $tag->term_id, 'thegem_swatches_tooltip_text', true );
?>
<tr class="form-field">
	<th scope="row" valign="top"><label for="thegem_color_display_type"><?php esc_html_e( 'Display type', 'thegem' ); ?></label></th>
	<td class="thegem-meta-display-type">
		<?php thegem_print_select_input(array('color' => __('Color', 'thegem'), 'image' => __('Image', 'thegem')), $display_type, 'thegem_color_display_type', 'thegem_color_display_type'); ?>
	</td>
</tr>
<tr class="form-field thegem-hide-on-image">
	<th scope="row" valign="top"><label for="thegem_color"><?php esc_html_e( 'Color', 'thegem' ); ?></label></th>
	<td class="thegem-meta-color">
		<input type="text" id="thegem_color" name="thegem_color" value="<?php echo esc_attr($value); ?>" class="color-select" style="width: auto;" />
	</td>
</tr>
<tr class="form-field thegem-hide-on-image">
	<th scope="row" valign="top"><label for="thegem_color_dual"><?php esc_html_e( 'Is Dual Color', 'thegem' ); ?></label></th>
	<td class="thegem-meta-color">
		<input type="checkbox" id="thegem_color_dual" name="thegem_color_dual" value="1" <?php checked($color_dual, 1); ?> />
	</td>
</tr>
<tr class="form-field thegem-hide-on-image thegem-term-color-2-wrap">
	<th scope="row" valign="top"><label for="thegem_color_2"><?php esc_html_e( 'Secondary Color', 'thegem' ); ?></label></th>
	<td class="thegem-meta-color-2">
		<input type="text" id="thegem_color_2" name="thegem_color_2" value="<?php echo esc_attr($color_2); ?>" class="color-select" style="width: auto;" />
	</td>
</tr>
<tr class="form-field thegem-hide-on-color">
	<th scope="row" valign="top"><label for="thegem_color_image"><?php esc_html_e( 'Image', 'thegem' ); ?></label></th>
	<td class="thegem-meta-image">
		<input type="hidden" id="thegem_color_image" name="thegem_color_image" value="<?php echo esc_attr($color_image); ?>" />
		<div id="thegem_attribute_color_image" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $color_image_src ); ?>" width="60px" height="60px" /></div>
		<div style="line-height: 60px;">
			<button type="button" class="upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'woocommerce' ); ?></button>
			<button type="button" class="remove_image_button button"><?php esc_html_e( 'Remove image', 'woocommerce' ); ?></button>
		</div>
		<script type="text/javascript">

			// Only show the "remove image" button when needed
			if ( '0' === jQuery( '#thegem_color_image' ).val() ) {
				jQuery( '.remove_image_button' ).hide();
			}

			// Uploading files
			var file_frame;

			jQuery( document ).on( 'click', '.upload_image_button', function( event ) {

				event.preventDefault();

				// If the media frame already exists, reopen it.
				if ( file_frame ) {
					file_frame.open();
					return;
				}

				// Create the media frame.
				file_frame = wp.media.frames.downloadable_file = wp.media({
					title: '<?php esc_html_e( 'Choose an image', 'woocommerce' ); ?>',
					button: {
						text: '<?php esc_html_e( 'Use image', 'woocommerce' ); ?>'
					},
					multiple: false
				});

				// When an image is selected, run a callback.
				file_frame.on( 'select', function() {
					var attachment           = file_frame.state().get( 'selection' ).first().toJSON();
					var attachment_thumbnail = attachment.sizes.thumbnail || attachment.sizes.full;

					jQuery( '#thegem_color_image' ).val( attachment.id );
					jQuery( '#thegem_attribute_color_image' ).find( 'img' ).attr( 'src', attachment_thumbnail.url );
					jQuery( '.remove_image_button' ).show();
				});

				// Finally, open the modal.
				file_frame.open();
			});

			jQuery( document ).on( 'click', '.remove_image_button', function() {
				jQuery( '#thegem_attribute_color_image' ).find( 'img' ).attr( 'src', '<?php echo esc_js( wc_placeholder_img_src() ); ?>' );
				jQuery( '#thegem_color_image' ).val( '' );
				jQuery( '.remove_image_button' ).hide();
				return false;
			});

		</script>
	</td>
</tr>
<tr class="form-field thegem-term-tooltip-text-wrap">
	<th scope="row" valign="top"><label for="thegem_swatches_tooltip_text"><?php esc_html_e( 'Tooltip text', 'thegem' ); ?></label></th>
	<td class="thegem-meta-tooltip-text">
		<input type="text" id="thegem_swatches_tooltip_text" name="thegem_swatches_tooltip_text" value="<?php echo esc_attr($tooltip_text); ?>" class="text" />
	</td>
</tr>
<script type="text/javascript">
jQuery( document ).on( 'change', '#thegem_color_display_type', function( event ) {
	jQuery('.thegem-hide-on-image, .thegem-hide-on-color').show();
	jQuery('.thegem-hide-on-' + jQuery(this).val()).hide();
	jQuery('#thegem_color_dual').trigger('change');
});
jQuery('#thegem_color_display_type').trigger('change');
jQuery( document ).on( 'change', '#thegem_color_dual', function( event ) {
	if(jQuery(this).is(':checked')) {
		jQuery('.thegem-term-color-2-wrap').show();
	} else {
		jQuery('.thegem-term-color-2-wrap').hide();
	}
});
jQuery('#thegem_color_dual').trigger('change');
</script>
<?php
}

function thegem_add_product_attribute_image_fields( $taxonomy ) {

?>
<table class="form-field">
	<tr class="form-field">
		<th scope="row" valign="top"><label for="thegem_image"><?php esc_html_e( 'Image', 'thegem' ); ?></label></th>
		<td class="thegem-meta-image">
			<div id="thegem_attribute_image" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" width="60px" height="60px" /></div>
			<div style="line-height: 60px;">
				<button type="button" class="upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'woocommerce' ); ?></button>
				<button type="button" class="remove_image_button button"><?php esc_html_e( 'Remove image', 'woocommerce' ); ?></button>
			</div>
			<input type="hidden" id="thegem_image" name="thegem_image" value="" />
			<script type="text/javascript">

				// Only show the "remove image" button when needed
				if ( ! jQuery( '#thegem_image' ).val() ) {
					jQuery( '.remove_image_button' ).hide();
				}

				// Uploading files
				var file_frame;

				jQuery( document ).on( 'click', '.upload_image_button', function( event ) {

					event.preventDefault();

					// If the media frame already exists, reopen it.
					if ( file_frame ) {
						file_frame.open();
						return;
					}

					// Create the media frame.
					file_frame = wp.media.frames.downloadable_file = wp.media({
						title: '<?php esc_html_e( 'Choose an image', 'woocommerce' ); ?>',
						button: {
							text: '<?php esc_html_e( 'Use image', 'woocommerce' ); ?>'
						},
						multiple: false
					});

					// When an image is selected, run a callback.
					file_frame.on( 'select', function() {
						var attachment           = file_frame.state().get( 'selection' ).first().toJSON();
						var attachment_thumbnail = attachment.sizes.thumbnail || attachment.sizes.full;

						jQuery( '#thegem_image' ).val( attachment.id );
						jQuery( '#thegem_attribute_image' ).find( 'img' ).attr( 'src', attachment_thumbnail.url );
						jQuery( '.remove_image_button' ).show();
					});

					// Finally, open the modal.
					file_frame.open();
				});

				jQuery( document ).on( 'click', '.remove_image_button', function() {
					jQuery( '#thegem_attribute_image' ).find( 'img' ).attr( 'src', '<?php echo esc_js( wc_placeholder_img_src() ); ?>' );
					jQuery( '#thegem_image' ).val( '' );
					jQuery( '.remove_image_button' ).hide();
					return false;
				});

				jQuery( document ).ajaxComplete( function( event, request, options ) {
					if ( request && 4 === request.readyState && 200 === request.status
						&& options.data && 0 <= options.data.indexOf( 'action=add-tag' ) ) {

						var res = wpAjax.parseAjaxResponse( request.responseXML, 'ajax-response' );
						if ( ! res || res.errors ) {
							return;
						}
						// Clear Thumbnail fields on submit
						jQuery( '#thegem_attribute_image' ).find( 'img' ).attr( 'src', '<?php echo esc_js( wc_placeholder_img_src() ); ?>' );
						jQuery( '#thegem_image' ).val( '' );
						jQuery( '.remove_image_button' ).hide();
						// Clear Display type field on submit
						return;
					}
				} );

			</script>
		</td>
	</tr>
</table>
<div class="form-field">
	<label for="thegem_swatches_tooltip_text"><?php esc_html_e( 'Tooltip Text', 'thegem' ); ?></label>
	<input type="text" id="thegem_swatches_tooltip_text" name="thegem_swatches_tooltip_text" value="" size="50%" class="text" />
</div>
<?php
}

function thegem_edit_product_attribute_image_fields( $tag, $taxonomy ) {
	$value = absint(get_term_meta( $tag->term_id, 'thegem_image', true ));
	$tooltip_text = get_term_meta( $tag->term_id, 'thegem_swatches_tooltip_text', true );
	$image = wc_placeholder_img_src();
	if ( $value ) {
		$image = wp_get_attachment_thumb_url( $value );
	}
?>
<tr class="form-field">
	<th scope="row" valign="top"><label for="thegem_image"><?php esc_html_e( 'Image', 'thegem' ); ?></label></th>
	<td class="thegem-meta-image">
		<input type="hidden" id="thegem_image" name="thegem_image" value="<?php echo esc_attr($value); ?>" />
		<div id="thegem_attribute_image" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $image ); ?>" width="60px" height="60px" /></div>
		<div style="line-height: 60px;">
			<button type="button" class="upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'woocommerce' ); ?></button>
			<button type="button" class="remove_image_button button"><?php esc_html_e( 'Remove image', 'woocommerce' ); ?></button>
		</div>
		<script type="text/javascript">

			// Only show the "remove image" button when needed
			if ( '0' === jQuery( '#thegem_image' ).val() ) {
				jQuery( '.remove_image_button' ).hide();
			}

			// Uploading files
			var file_frame;

			jQuery( document ).on( 'click', '.upload_image_button', function( event ) {

				event.preventDefault();

				// If the media frame already exists, reopen it.
				if ( file_frame ) {
					file_frame.open();
					return;
				}

				// Create the media frame.
				file_frame = wp.media.frames.downloadable_file = wp.media({
					title: '<?php esc_html_e( 'Choose an image', 'woocommerce' ); ?>',
					button: {
						text: '<?php esc_html_e( 'Use image', 'woocommerce' ); ?>'
					},
					multiple: false
				});

				// When an image is selected, run a callback.
				file_frame.on( 'select', function() {
					var attachment           = file_frame.state().get( 'selection' ).first().toJSON();
					var attachment_thumbnail = attachment.sizes.thumbnail || attachment.sizes.full;

					jQuery( '#thegem_image' ).val( attachment.id );
					jQuery( '#thegem_attribute_image' ).find( 'img' ).attr( 'src', attachment_thumbnail.url );
					jQuery( '.remove_image_button' ).show();
				});

				// Finally, open the modal.
				file_frame.open();
			});

			jQuery( document ).on( 'click', '.remove_image_button', function() {
				jQuery( '#thegem_attribute_image' ).find( 'img' ).attr( 'src', '<?php echo esc_js( wc_placeholder_img_src() ); ?>' );
				jQuery( '#thegem_image' ).val( '' );
				jQuery( '.remove_image_button' ).hide();
				return false;
			});

		</script>
	</td>
</tr>
<tr class="form-field thegem-term-tooltip-text-wrap">
	<th scope="row" valign="top"><label for="thegem_swatches_tooltip_text"><?php esc_html_e( 'Label', 'thegem' ); ?></label></th>
	<td class="thegem-meta-tooltip-text">
		<input type="text" id="thegem_swatches_tooltip_text" name="thegem_swatches_tooltip_text" value="<?php echo esc_attr($tooltip_text); ?>" class="text" />
	</td>
</tr>
<?php
}

function thegem_add_product_attribute_label_fields( $taxonomy ) {
?>
<div class="form-field">
	<label for="thegem_label"><?php esc_html_e( 'Label', 'thegem' ); ?></label>
	<input type="text" id="thegem_label" name="thegem_label" value="" size="50%" class="text" />
</div>
<div class="form-field">
	<label for="thegem_swatches_tooltip_text"><?php esc_html_e( 'Tooltip Text', 'thegem' ); ?></label>
	<input type="text" id="thegem_swatches_tooltip_text" name="thegem_swatches_tooltip_text" value="" size="50%" class="text" />
</div>
<?php
}

function thegem_edit_product_attribute_label_fields( $tag, $taxonomy ) {
	$value = get_term_meta( $tag->term_id, 'thegem_label', true );
	$tooltip_text = get_term_meta( $tag->term_id, 'thegem_swatches_tooltip_text', true );
?>
<tr class="form-field thegem-term-label-wrap">
	<th scope="row" valign="top"><label for="thegem_label"><?php esc_html_e( 'Label', 'thegem' ); ?></label></th>
	<td class="thegem-meta-label">
		<input type="text" id="thegem_label" name="thegem_label" value="<?php echo esc_attr($value); ?>" class="text" />
	</td>
</tr>
<tr class="form-field thegem-term-tooltip-text-wrap">
	<th scope="row" valign="top"><label for="thegem_swatches_tooltip_text"><?php esc_html_e( 'Tooltip text', 'thegem' ); ?></label></th>
	<td class="thegem-meta-tooltip-text">
		<input type="text" id="thegem_swatches_tooltip_text" name="thegem_swatches_tooltip_text" value="<?php echo esc_attr($tooltip_text); ?>" class="text" />
	</td>
</tr>
<?php
}

function thegem_variation_attribute_options_html( $html, $args ) {
	$attribute_data = wc_get_attribute(wc_attribute_taxonomy_id_by_name($args['attribute']));
	if(!empty($attribute_data) && ($attribute_data->type == 'color' || $attribute_data->type == 'image' || $attribute_data->type == 'label')) {
		// Get selected value.
		if ( false === $args['selected'] && $args['attribute'] && $args['product'] instanceof WC_Product ) {
			$selected_key = 'attribute_' . sanitize_title( $args['attribute'] );
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			$args['selected'] = isset( $_REQUEST[ $selected_key ] ) ? wc_clean( wp_unslash( $_REQUEST[ $selected_key ] ) ) : $args['product']->get_variation_default_attribute( $args['attribute'] );
			// phpcs:enable WordPress.Security.NonceVerification.Recommended
		}

		$options               = $args['options'];
		$product               = $args['product'];
		$attribute             = $args['attribute'];
		$name                  = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
		$id                    = $args['id'] ? $args['id'] : sanitize_title( $attribute );
		$class                 = 'gem-attribute-selector type-'.$attribute_data->type;
		$show_option_none      = (bool) $args['show_option_none'];
		$show_option_none_text = $args['show_option_none'] ? $args['show_option_none'] : __( 'Choose an option', 'woocommerce' ); // We'll do our best to hide the placeholder, but we'll need to show something when resetting options.

		if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
			$attributes = $product->get_variation_attributes();
			$options    = $attributes[ $attribute ];
		}
		$html = '<div class="field-input">' . $html . '</div>';
		$html = '<div id="' . esc_attr( $id ) . '" class="' . esc_attr( $class ) . '" data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '">' . $html;
		//$html .= '<input type="hidden" name="' . esc_attr( $name ) . '" value="' . sanitize_title( $args['selected'] ) . '" >';
		if ( ! empty( $options ) ) {
			if ( $product && taxonomy_exists( $attribute ) ) {
				// Get terms if this is a taxonomy - ordered. We need the names too.
				$terms = wc_get_product_terms(
					$product->get_id(),
					$attribute,
					array(
						'fields' => 'all',
					)
				);

				$html .= '<ul class="styled gem-attribute-options">';
				foreach ( $terms as $term ) {
					if ( in_array( $term->slug, $options, true ) ) {
						$html .= '<li data-value="' . esc_attr( $term->slug ) . '"' . (sanitize_title( $args['selected'] ) == $term->slug ? ' class="selected"' : '') . '>';
						if ($attribute_data->type == 'color') {
							$display_type = get_term_meta( $term->term_id, 'thegem_color_display_type', true );
							$color = get_term_meta( $term->term_id, 'thegem_color', true );
							$color_dual = get_term_meta( $term->term_id, 'thegem_color_dual', true );
							$color_2 = get_term_meta( $term->term_id, 'thegem_color_2', true );
							$color_image = get_term_meta( $term->term_id, 'thegem_color_image', true );
							$image = absint(get_term_meta( $term->term_id, 'thegem_color_image', true ));
							$image_thumb = wc_placeholder_img_src();
							if ( $image ) {
								$image_thumb_data = thegem_generate_thumbnail_src($image, 'thegem-post-thumb-medium');
								$image_thumb = $image_thumb_data[0];
							}
							if($display_type === 'image') {
								$html .= '<span class="color"' . (!empty($image_thumb) ? ' style="background-image: url(' . esc_attr($image_thumb) . ');"' : '') . '></span>';
							} else {
								if($color_dual) {
									$html .= '<span class="color"' . (!empty($color) && !empty($color_2) ? ' style="background-image: linear-gradient(90deg, ' . esc_attr($color).' 50%, ' . esc_attr($color_2) . ' 50%);"' : '') . '></span>';
								} else {
									$html .= '<span class="color"' . (!empty($color) ? ' style="background-color: ' . esc_attr($color) . ';"' : '') . '></span>';
								}
							}
						} elseif ($attribute_data->type == 'image') {
							$attribute_slug = preg_replace( '/^pa\_/', '', $attribute_data->slug );
							$tooltip_type = get_option('thegem_attribute_'.$attribute_slug.'_image_tooltip_type');
							$image = absint(get_term_meta( $term->term_id, 'thegem_image', true ));
							$image_thumb = $image_hover = wc_placeholder_img_src();
							$label = get_term_meta( $term->term_id, 'thegem_label', true );
							$label = empty($label) ? $term->name : $label;
							$tooltip_text = get_term_meta( $term->term_id, 'thegem_swatches_tooltip_text', true );
							$tooltip_text = $tooltip_text ? $tooltip_text : $label;
							if ( $image ) {
								$image_thumb_data = thegem_generate_thumbnail_src($image, 'thegem-post-thumb-medium');
								$image_thumb = $image_thumb_data[0];
								if($tooltip_type !== 'text') {
									$image_hover_data = thegem_generate_thumbnail_src($image, 'thegem-blog-timeline');
									$image_hover = $image_hover_data[0];
								}
							}
							$html .= '<span class="image"' . (!empty($image) ? ' style="background-image: url(' . esc_attr($image_thumb).');"' : '') . '></span>';
							if($tooltip_type !== 'text') {
								$html .= '<span class="image-hover"><img src="'.$image_hover.'" alt="#"/>' . ($tooltip_type !== 'image' ? '<span class="text">' . esc_html($tooltip_text) . '</span>' : '') .'</span>';
							}
						} else {
							$label = get_term_meta( $term->term_id, 'thegem_label', true );
							$label = empty($label) ? $term->name : $label;
							$html .= '<span class="label">' . esc_html($label) . '</span>';
						}
						$tooltip_text = get_term_meta( $term->term_id, 'thegem_swatches_tooltip_text', true );
						$tooltip_text = $tooltip_text ? $tooltip_text : apply_filters( 'woocommerce_variation_option_name', $term->name, $term, $attribute, $product );
						$html .= '<span class="text">' . esc_html( $tooltip_text ) . '</span>';
						$html .= '</li>';
					}
				}
				$html .= '</ul>';
			}
		}
		$html .= '</div>';
	}
	return $html;
}
add_filter( 'woocommerce_dropdown_variation_attribute_options_html', 'thegem_variation_attribute_options_html', 10, 2 );

function thegem_woocommerce_product_option_terms( $attribute_taxonomy, $i, $attribute ) {
	if ( 'color' !== $attribute_taxonomy->attribute_type && 'image' !== $attribute_taxonomy->attribute_type && 'label' !== $attribute_taxonomy->attribute_type ) {
		return;
	}
?>
<select multiple="multiple" data-placeholder="<?php esc_attr_e( 'Select terms', 'woocommerce' ); ?>" class="multiselect attribute_values wc-enhanced-select" name="attribute_values[<?php echo esc_attr( $i ); ?>][]">
	<?php
	$args      = array(
		'orderby'    => ! empty( $attribute_taxonomy->attribute_orderby ) ? $attribute_taxonomy->attribute_orderby : 'name',
		'hide_empty' => 0,
	);
	$all_terms = get_terms( $attribute->get_taxonomy(), apply_filters( 'woocommerce_product_attribute_terms', $args ) );
	if ( $all_terms ) {
		foreach ( $all_terms as $term ) {
			$options = $attribute->get_options();
			$options = ! empty( $options ) ? $options : array();
			echo '<option value="' . esc_attr( $term->term_id ) . '"' . wc_selected( $term->term_id, $options ) . '>' . esc_html( apply_filters( 'woocommerce_product_attribute_term_name', $term->name, $term ) ) . '</option>';
		}
	}
	?>
</select>
<button class="button plus select_all_attributes"><?php esc_html_e( 'Select all', 'woocommerce' ); ?></button>
<button class="button minus select_no_attributes"><?php esc_html_e( 'Select none', 'woocommerce' ); ?></button>
<button class="button fr plus add_new_attribute"><?php esc_html_e( 'Add new', 'woocommerce' ); ?></button>
<?php
}
add_filter( 'woocommerce_product_option_terms', 'thegem_woocommerce_product_option_terms', 10, 3 );
