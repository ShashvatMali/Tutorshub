<?php

/**
 * Instructor short description
 *
 * @package     Tuturn
 * @subpackage  Tuturn/templates/nstructor-loop
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
global $post;?>
<div class="tu-listinginfo_description">
    <p><?php echo wp_trim_words(get_the_content(), 38, false); ?></p> 
 </div>
 