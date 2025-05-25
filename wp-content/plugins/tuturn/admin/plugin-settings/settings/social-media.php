<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Api Settings
 *
 * @package     Tuturn
 * @subpackage  Tuturn/admin/Plugin_Settings/Settings
 * @author      Amentotech <info@amentotech.com>
 * @link        http://amentotech.com/
 * @version     1.0
 * @since       1.0
*/
$media_array        = array();
$media_urls         = tuturn_social_media_lists();
foreach($media_urls as $key => $val ){
    $media_array[$key]  = !empty($val['name']) ? $val['name'] : '';
}
Redux::setSection( $opt_name,
    array(
        'title'       => esc_html__( 'Social Media', 'tuturn' ),
        'id'          => 'tu-social-media-settings',
        'subsection'  => false,
        'desc'       	=> '',
        'icon'       	=> 'el el-share-alt',
        'fields'      => array(
            array(
                'id'       => 'social_media',
                'type'     => 'select',
                'multi'    => true,
                'title'    => esc_html__('Social media', 'tuturn'), 
                'desc'     => esc_html__('Select social media to display option on user profile.', 'tuturn'),
                'options'  => $media_array,
                'default'  => array('facebook','twitter','linkedin')
            )
        )
    )
);
