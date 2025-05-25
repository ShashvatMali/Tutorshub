<?php
namespace Pagelayoutsettings;
/**
 * Class 'Tuturn_Page_Layout_Settings' defines the page layout settings
 *
 * @package     Tuturn
 * @subpackage  Tuturn/admin/partials
 * @author      Amentotech <info@amentotech.com>
 * @link        http://amentotech.com/
 * @version     1.0
 * @since       1.0
 */
class Tuturn_Page_Layout_Settings{

	/**
     * constructor
     *
     * @since    1.0.0
     * @access   public
     */
    public function __construct() {
        add_action('init', array(&$this, 'init_page_layout_settings'));
		/* Save Meta Details */
		add_action('save_post', array(&$this, 'tuturn_save_page_layout'));
    }

	/**
	 * page layout settings
	 */
	public function init_page_layout_settings(){
		$this->init_sidebars_taxonomy();
		add_action( 'add_meta_boxes', array(&$this, 'tuturn_metabox_page_layout') );
	}

	/**
	 * Add meta field for page layout
	 */
	public function tuturn_metabox_page_layout() {
		add_meta_box('tuturn-page-layout', __( 'Page Layout', 'tuturn' ), array(&$this, 'tuturn_page_layout_callback'), 'page', 'side', 'core');
	}

	/**
	 * @Init sidebars 
	 * @return {post}
	 *  */ 
	public function init_sidebars_taxonomy() {
		$sidebars_labels = array(
			'name' 				=> esc_html__('Sidebars', 'tuturn'),
			'singular_name' 	=> esc_html__('Sidebar','tuturn'),
			'search_items' 		=> esc_html__('Search sidebar', 'tuturn'),
			'all_items' 		=> esc_html__('All sidebars', 'tuturn'),
			'parent_item' 		=> esc_html__('Parent sidebar', 'tuturn'),
			'parent_item_colon' => esc_html__('Parent sidebar:', 'tuturn'),
			'edit_item' 		=> esc_html__('Edit sidebar', 'tuturn'),
			'update_item' 		=> esc_html__('Update sidebar', 'tuturn'),
			'add_new_item' 		=> esc_html__('Add new sidebar', 'tuturn'),
			'new_item_name' 	=> esc_html__('New sidebar name', 'tuturn'),
			'menu_name' 		=> esc_html__('Sidebars', 'tuturn'),
		);

		$sidebars_args = array(
			'hierarchical' 			=> false,
			'labels' 				=> $sidebars_labels,
			'public' 				=> false,
			'show_in_nav_menus' 	=> false,
			'show_ui' 				=> true,
			'query_var' 			=> false,
			'rewrite' 				=> false,
		);
		
		register_taxonomy('tu_sidebars', 'tuturn-instructor', $sidebars_args);
		
		$sidebars = get_terms( array(
			'taxonomy' 		=> 'tu_sidebars',
			'hide_empty' 	=> false,
		) );

		foreach ( $sidebars as $sidebar ) {
			register_sidebar(
				array(
					'id'            => 'tu-' . sanitize_title( $sidebar->name ),
					'name'          => $sidebar->name,
					'description'   => $sidebar->description,
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget' 	=> '</div>',
					'before_title' 	=> '<div class="tu-sidetitle"><h2>',
					'after_title' 	=> '</h2></div>',
				)
			);
		}
	}

	/**
     * Page layout callback
    */
	function tuturn_page_layout_callback($post){
		/* page layouts */
		$page_layout		= get_post_meta($post->ID, 'tuturn_pagelayout_setting', true);
		$page_layout_style	= !empty($page_layout) ? $page_layout : '';
		/* sidebars */
		$all_sidebars			= tuturnGetRegisterSidebars();
		$page_sidebar 			= get_post_meta($post->ID, 'tuturn_pagelayout_sidebar', true);
		$sidebar_selected 		= !empty($page_sidebar) ? $page_sidebar : '';
		$style_class			= ($page_layout_style === 'none') ? 'style="display:none"' : 'style="display:block"';
		?>
		<div class="tu-sidebar-layout">
			<label><?php esc_html_e('Choose page layout style', 'tuturn'); ?></label>
			<ul>
				<li class="tu-layout-select">
					<label class="tu-image-select side-layout_1" for="side-layout_1">
						<input type="radio" class="tu-page-layout" id="side-layout_1" name="page_side_layout" value="none" <?php if($page_layout_style === 'none'){echo esc_attr('checked');} ?>>
						<img src="<?php echo esc_url(TUTURN_DIRECTORY_URI.'public/images/page-layout/full-width.png'); ?>" title="<?php esc_attr_e('1 column','tuturn'); ?>" alt="<?php esc_attr_e('1 Column','tuturn'); ?>">
					</label>
				</li>
				<li class="tu-layout-select">
					<label class="tu-image-select side-layout_2" for="side-layout_2">
						<input type="radio" class="tu-page-layout" id="side-layout_2" name="page_side_layout" value="left" <?php if($page_layout_style === 'left'){echo esc_attr('checked');} ?>>
						<img src="<?php echo esc_url(TUTURN_DIRECTORY_URI.'public/images/page-layout/left-sidebar.png'); ?>" title="<?php esc_attr_e('2 column left','tuturn'); ?>" alt="<?php esc_attr_e('2 Column Left','tuturn'); ?>">
					</label>
				</li>
				<li class="tu-layout-select">
					<label class="tu-image-select side-layout_3" for="side-layout_3">
						<input type="radio" class="tu-page-layout" id="side-layout_3" name="page_side_layout" value="right" <?php if($page_layout_style === 'right'){echo esc_attr('checked');} ?>>
						<img src="<?php echo esc_url(TUTURN_DIRECTORY_URI.'public/images/page-layout/right-sidebar.png'); ?>" title="<?php esc_attr_e('2 column right','tuturn'); ?>" alt="<?php esc_attr_e('2 Column Right','tuturn'); ?>">
					</label>
				</li>
			</ul>
		</div>
		<?php $style_class = ($page_layout_style === 'none') ? 'style=display:none' : 'style=display:block'; ?>
			<div class="tu-page-sidebar" <?php echo do_shortcode($style_class);?>>
				<label><?php esc_html_e('Choose sidebar', 'tuturn'); ?></label>
				<select name="page_sidebar" id="page-sidebar">
					<?php 
						if(!empty($all_sidebars)){
							foreach($all_sidebars as $key=>$val){
								$is_selected = $sidebar_selected === $key ? 'selected' : '';
								?>
								<option value="<?php echo esc_attr($key); ?>" <?php echo esc_attr($is_selected);?>><?php echo esc_html($val);?></option>
								<?php
							}
						}
					?>			
				</select>
			</div>
		<?php
	}

	/**
     * Save page layout settings
    */
	public function tuturn_save_page_layout($post_id){
		// Autosave, do nothing
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		// AJAX? Not used here
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}
		// Check user permissions
		if ( ! current_user_can( 'edit_post', $post_id ) ){
			return;
		} 
		
		// update post for hourly rate
		$post_type =  get_post_type( $post_id );
		if ( !empty($post_type) && $post_type === 'tuturn-instructor' ) {
			$hourly_rate = isset( $_POST['hourly_rate'] ) ? $_POST['hourly_rate'] : 0;
			update_post_meta($post_id, 'hourly_rate', $hourly_rate);
		}

		// Only set for post_type = page!
		if (  $post_type !== 'page'  ) {
			return;
		}

		/* page layout */
		$page_layout_style = isset( $_POST['page_side_layout'] ) ? $_POST['page_side_layout'] : 'none';
		update_post_meta($post_id, 'tuturn_pagelayout_setting', $page_layout_style);

		/* page sidebar */
		$page_layout_sidebar = isset( $_POST['page_sidebar'] ) ? $_POST['page_sidebar'] : '';
		update_post_meta($post_id, 'tuturn_pagelayout_sidebar', $page_layout_sidebar);

	}
}
new Tuturn_Page_Layout_Settings();