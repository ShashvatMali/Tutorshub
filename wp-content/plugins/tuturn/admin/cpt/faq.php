<?php
namespace FaqsPosttypeCpt;
/**
 * Class 'Tuturn_Admin_CPT_FAQ' defines the cusotm post type
 *
 * @package     Tuturn
 * @subpackage  Tuturn/admin/cpt
 * @author      Amentotech <info@amentotech.com>
 * @link        http://amentotech.com/
 * @version     1.0
 * @since       1.0
 */
class Tuturn_CPT_FAQ {

    /**
     * FAQ post type
     *
     * @since    1.0.0
     * @access   public
     */
    public function __construct() {
        add_action('init', array(&$this, 'init_post_type'));
         // FAQ category image
         if(!empty($_REQUEST['taxonomy']) && $_REQUEST['taxonomy'] == 'faq_categories'){
            add_action( 'faq_categories_add_form_fields', array ( $this, 'add_faq_category_image' ), 10, 2 );
            add_action( 'created_faq_categories', array ( $this, 'save_faq_category_image' ), 10, 2 );
            add_action( 'faq_categories_edit_form_fields', array ( $this, 'update_faq_category_image' ), 10, 2 );
            add_action( 'edited_faq_categories', array ( $this, 'updated_faq_category_image' ), 10, 2 );
            add_action( 'admin_enqueue_scripts', array( $this, 'load_media' ) );
            add_action( 'admin_footer', array ( $this, 'add_script' ) );
        }
    }

    /**
     * @Init post type
     */
    public function init_post_type() {
        $this->register_posttype();
        $this->register_taxonomy();
    }

    /**
     *Regirster FAQ post type
    */
    public function register_posttype() {

        $labels = array(
            'name'                  => esc_html__( 'FAQ', 'tuturn' ),
            'singular_name'         => esc_html__( 'FAQ', 'tuturn' ),
            'menu_name'             => esc_html__( 'FAQ', 'tuturn' ),
            'name_admin_bar'        => esc_html__( 'FAQ', 'tuturn' ),
            'archives'              => esc_html__( 'FAQ Archives', 'tuturn' ),
            'attributes'            => esc_html__( 'FAQ Attributes', 'tuturn' ),
            'parent_item_colon'     => esc_html__( 'Parent FAQ:', 'tuturn' ),
            'all_items'             => esc_html__( 'All FAQ', 'tuturn' ),
            'add_new_item'          => esc_html__( 'Add New FAQ', 'tuturn' ),
            'add_new'               => esc_html__( 'Add New FAQ', 'tuturn' ),
            'new_item'              => esc_html__( 'New FAQ', 'tuturn' ),
            'edit_item'             => esc_html__( 'Edit FAQ', 'tuturn' ),
            'update_item'           => esc_html__( 'Update FAQ', 'tuturn' ),
            'view_item'             => esc_html__( 'View FAQ', 'tuturn' ),
            'view_items'            => esc_html__( 'View FAQ', 'tuturn' ),
            'search_items'          => esc_html__( 'Search FAQ', 'tuturn' ),
            'not_found'             => esc_html__( 'Not found', 'tuturn' ),
            'not_found_in_trash'    => esc_html__( 'Not found in Trash', 'tuturn' ),
            'featured_image'        => esc_html__( 'Featured Image', 'tuturn' ),
            'set_featured_image'    => esc_html__( 'Set featured image', 'tuturn' ),
            'remove_featured_image' => esc_html__( 'Remove featured image', 'tuturn' ),
            'use_featured_image'    => esc_html__( 'Use as featured image', 'tuturn' ),
            'insert_into_item'      => esc_html__( 'Insert into Profile', 'tuturn' ),
            'uploaded_to_this_item' => esc_html__( 'Uploaded to this Profile', 'tuturn' ),
            'items_list'            => esc_html__( 'FAQ list', 'tuturn' ),
            'items_list_navigation' => esc_html__( 'FAQ list navigation', 'tuturn' ),
            'filter_items_list'     => esc_html__( 'Filter FAQ list', 'tuturn' ),
        );

        $args = array(
            'label'                 => esc_html__( 'FAQ', 'tuturn' ),
            'description'           => esc_html__( 'All FAQs', 'tuturn' ),
            'labels'                => apply_filters('tuturn_faq_cpt_labels', $labels),
            'supports'              => array( 'title','editor'),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-admin-users',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'page',
            'show_in_rest'          => true,
            'rest_base'             => 'FAQ',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
            'show_in_menu' 			=> 'edit.php?post_type=tuturn-instructor',
        );
        register_post_type( apply_filters('tuturn_faq_post_type_name', 'faq'), $args );

    }

    /**
     *Regirster FAQ categories
    */
    public function register_taxonomy() {
        $cat_labels = array(
            'name' 					=> esc_html__('FAQ categories', 'tuturn'),
            'singular_name' 	    => esc_html__('Category','tuturn'),
            'search_items'			=> esc_html__('Search FAQ category', 'tuturn'),
            'all_items' 		    => esc_html__('All Category', 'tuturn'),
            'parent_item' 			=> esc_html__('Parent FAQ category', 'tuturn'),
            'parent_item_colon'     => esc_html__('Parent Category:', 'tuturn'),
            'edit_item' 		    => esc_html__('Edit Category', 'tuturn'),
            'update_item' 			=> esc_html__('Update Category', 'tuturn'),
            'add_new_item' 			=> esc_html__('Add New FAQ category', 'tuturn'),
            'new_item_name' 	    => esc_html__('New Category Name', 'tuturn'),
            'menu_name' 		    => esc_html__('FAQ categories', 'tuturn'),
        );

        $cat_args = array(
            'hierarchical'              => true,
            'labels'			        => apply_filters('tuturn_faq_taxonomy_labels', $cat_labels),
            'show_ui'                   => true,
            'show_admin_column'         => true,
            'query_var'                 => true,
            'rewrite'                   => array('slug' => 'faq_categories'),
            'show_in_rest'              => true,
            'show_in_nav_menus' 	    => false,
            'show_in_menu'              => true,
            'rest_base'                 => 'faq_categories',
            'rest_controller_class'     => 'WP_REST_Terms_Controller',
        );

        register_taxonomy('faq_categories', array('faq'), $cat_args);
    }

    
        /**
        * Add media file
        * @since 1.0.0
        */
        public function load_media() {
            wp_enqueue_media();
        }

        /**
        * Add a faq category image field
        * @since 1.0.0
        */
        public function add_faq_category_image ( $taxonomy ) { ?>
            <div class="form-field term-group">
            <label for="category-image-id"><?php esc_html_e('FAQ Category Image', 'tuturn'); ?></label>
            <input type="hidden" id="category-image-id" name="category-image-id" class="custom_media_url" value="">
            <div id="category-image-wrapper"></div>
            <p>
                <input type="button" class="button button-secondary tu_tax_media_button" id="tu_tax_media_button" name="tu_tax_media_button" value="<?php esc_attr_e( 'Add Image', 'tuturn' ); ?>" />
                <input type="button" class="button button-secondary tu_tax_media_remove" id="tu_tax_media_remove" name="tu_tax_media_remove" value="<?php esc_attr_e( 'Remove Image', 'tuturn' ); ?>" />
            </p>
            </div>
        <?php
        }

        /**
        * Save the faq category image field
        * @since 1.0.0
        */
        public function save_faq_category_image ( $term_id, $tt_id ) {
            if( isset( $_POST['category-image-id'] ) && '' !== $_POST['category-image-id'] ){
                $image = esc_html( $_POST['category-image-id'] );
                add_term_meta( $term_id, 'category-image-id', $image, true );
            }
        }

       /**
        * Edit the faq category image field
        * @since 1.0.0
        */
        public function update_faq_category_image ( $term, $taxonomy ) { ?>
            <tr class="form-field term-group-wrap">
                <th scope="row">
                    <label for="category-image-id"><?php esc_html_e( 'FAQ Category Image', 'tuturn' ); ?></label>
                </th>
                <td>
                    <?php $image_id = get_term_meta ( $term -> term_id, 'category-image-id', true ); ?>
                    <input type="hidden" id="category-image-id" name="category-image-id" value="<?php echo (int)$image_id; ?>">
                    <div id="category-image-wrapper">
                    <?php if ( $image_id ) { ?>
                        <?php echo wp_get_attachment_image ( $image_id, 'thumbnail' ); ?>
                    <?php } ?>
                    </div>
                    <p>
                    <input type="button" class="button button-secondary tu_tax_media_button" id="tu_tax_media_button" name="tu_tax_media_button" value="<?php esc_attr_e( 'Add Image', 'tuturn' ); ?>" />
                    <input type="button" class="button button-secondary tu_tax_media_remove" id="tu_tax_media_remove" name="tu_tax_media_remove" value="<?php esc_attr_e( 'Remove Image', 'tuturn' ); ?>" />
                    </p>
                </td>
            </tr>
        <?php
        }

        /**
        * Add script
        * @since 1.0.0
        */
        public function add_script() { ?>
            <script>
                jQuery(document).ready( function($) {
                    function tuturn_faq_media_upload(button_class) {
                        var _custom_media = true,
                        _faq_orig_send_attachment = wp.media.editor.send.attachment;
                        $('body').on('click', button_class, function(e) {
                            var button_id = '#'+$(this).attr('id');
                            var send_attachment_bkp = wp.media.editor.send.attachment;
                            var button = $(button_id);
                            _faq_custom_media = true;
                            wp.media.editor.send.attachment = function(props, attachment){
                                if ( _faq_custom_media ) {
                                    $('#category-image-id').val(attachment.id);
                                    $('#category-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
                                    $('#category-image-wrapper .custom_media_image').attr('src',attachment.url).css('display','block');
                                } else {
                                    return _faq_orig_send_attachment.apply( button_id, [props, attachment] );
                                }
                            }
                        wp.media.editor.open(button);
                            return false;
                        });
                    }
                    
                    tuturn_faq_media_upload('.tu_tax_media_button.button'); 
                    $('body').on('click','.tu_tax_media_remove',function(){
                        $('#category-image-id').val('');
                        $('#category-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
                    });
                
                    $(document).ajaxComplete(function(event, xhr, settings) {
                        var queryStringArr = settings.data.split('&');
                        if( $.inArray('action=add-tag', queryStringArr) !== -1 ){
                            var xml = xhr.responseXML;
                            $response = $(xml).find('term_id').text();
                            if($response!=""){
                                // Clear the thumb image
                                $('#category-image-wrapper').html('');
                            }
                        }
                    });
                });
            </script>
            <?php 
        }

        /*
        * Update the faq category field value
        * @since 1.0.0
        */
        public function updated_faq_category_image( $term_id, $tt_id ) {
          
            if( isset( $_POST['category-image-id'] ) && '' !== $_POST['category-image-id'] ){
                $image = esc_html( $_POST['category-image-id']);
                update_term_meta ( $term_id, 'category-image-id', $image );
            } else {
                update_term_meta ( $term_id, 'category-image-id', '' );
            }
        }
}

new Tuturn_CPT_FAQ();
