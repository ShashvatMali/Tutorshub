<?php
/**
 * Register Email Templates
 *
 * @package     Ttuturn
 * @subpackage  Ttuturn/templates
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/
$dir = TUTURN_DIRECTORY;
include tuturn_load_template( 'helpers/email-helper' );

$scan_PostTypes = glob($dir."helpers/templates/*.*");
if( !empty( $scan_PostTypes ) ){
	foreach ($scan_PostTypes as $filename) {
		$file = pathinfo($filename);
    	if( !empty( $file['filename'] ) ){
			@include tuturn_load_template( 'helpers/templates/'.$file['filename'] );
		}
	}
}