<?php
/**
 *
 * Student profile description
 *
 * @package     Tuturn
 * @subpackage  Tuturn/Templates/Single_tuturn_Instructor
 * @author      Amentotech <info@amentotech.com>
 * @link       https://themeforest.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/
if(empty(get_the_content())){
    return;
}
?>
<div class="tu-detailstabs">    
    <div class="tab-content tu-tab-content" id="tuTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            <div class="tu-tabswrapper">
                <div class="tu-tabstitle">
                    <h4><?php esc_html_e('A brief introduction', 'tuturn');?></h4>
                </div>
                <div class="tu-description">
                    <?php echo apply_filters('the_content', get_the_content()); ?>
                </div>
            </div>
        </div>
    </div>
</div>