<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * 
 * Template to display package tab meta fields
 *
 * @package     Tuturn
 * @subpackage  Tuturn/admin/partials
 * @author      Amentotech <info@amentotech.com>
 * @link        http://amentotech.com/
 * @version     1.0
 * @since       1.0
 */
global $woocommerce, $post;
?>
<div class="options_group product-data-packages-feilds">
    <?php do_action('tuturn_packages_fields_before', $post->ID);
    $tuturn_package_field = Tuturn_Packages_Feilds::package_fields();
    $tuturn_package       = get_post_meta( get_the_ID(), 'tuturn_package_detail', true );
    foreach($tuturn_package_field as $field_id => $field){

        if(!empty($field['type'])){
            $label  = !empty($field['label']) ? $field['label'] : '';
            $description  = !empty($field['description']) ? $field['description'] : '';
            $classes      = !empty($field['classes']) ? $field['classes'] : '';
            $field_value  = !empty($tuturn_package[$field_id]) ? $tuturn_package[$field_id] : '';
            switch ($field['type']) {
                case 'select':   
                    $options  = !empty($field['options']) ? $field['options'] : '';
                    woocommerce_wp_select( array(
                        'id'          => 'package['.$field_id.']',
                        'value'       => $field_value,
                        'label'       => $label,
                        'options'     => $options,
                        'class' 		=> $classes,
                        'description'   => esc_html($description),
                    ) );                 
                    break;
                case 'text': 
                    woocommerce_wp_text_input( array(
                        'id'            => 'package['.$field_id.']',
                        'value'         => $field_value,
                        'label'         => $label,
                        'class' 		=> $classes,
                        'description'   => esc_html($description),
                        'custom_attributes' => array(
                            'step' 	=> 'any',
                            'min'	=> '0'
                        )     
                    ) );                  
                    break;
                case 'checkbox':  
                    woocommerce_wp_checkbox( array(
                        'id'            => 'package['.$field_id.']',
                        'value'         => $field_value,
                        'label'         => $label,
                        'class' 		=> $classes,
                        'description'   => esc_html($description),
                    ) );                  
                    break;
                default:
                    do_action('tuturn_package_field_render', $field, $field_id, $post->ID);
            }

        }
    }  
    do_action('tuturn_packages_fields_after', $post->ID);?>
</div>