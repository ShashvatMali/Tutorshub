<?php

/**
 * Instructor tabs 
 *
 * @package     Tuturn
 * @subpackage  Tuturn/Templates/Single_tuturn_Instructor
 * @author      Amentotech <info@amentotech.com>
 * @link       https://themeforest.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
$current_url    = get_permalink();
$tab  = !empty($_GET['tab']) ? $_GET['tab'] : 'home';
?>
<div class="tu-detailstabs">
    <ul class="nav nav-tabs tu-nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link<?php if($tab == 'home'){ echo esc_attr(' active');}?>" id="home-tab" href="<?php echo esc_url(add_query_arg('tab', 'home', $current_url));?>"><i class="icon icon-home"></i><span><?php esc_html_e('Introduction', 'tuturn'); ?></span></a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link<?php if($tab == 'reviews'){ echo esc_attr(' active');}?>" id="profile-tab" href="<?php echo esc_url(add_query_arg('tab', 'reviews', $current_url));?>"><i class="icon icon-message-circle"></i><span><?php esc_html_e('Reviews', 'tuturn'); ?></span></a>
        </li>
    </ul>
    <div class="tab-content tu-tab-content" id="tuTabContent">
        <?php if($tab == 'home'){?>
            <div class="tab-pane fade<?php if($tab == 'home'){ echo esc_attr(' show active');}?>" id="home" role="tabpanel" aria-labelledby="home-tab">
                <?php tuturn_get_template( 'single-tuturn-instructor/home-tab.php',$args);?>
                <?php 
                    tuturn_get_template('single-tuturn-instructor/availibility-details.php');
                    if (!empty($args['package_info']['gallery'])) {
                        tuturn_get_template('single-tuturn-instructor/media-gallery.php');
                    } 
                   
                ?>
            </div>
        <?php }elseif($tab == 'reviews'){?>
            <div class="tab-pane fade<?php if($tab == 'reviews'){ echo esc_attr(' show active');}?>" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                <?php tuturn_get_template( 'single-tuturn-instructor/reviews-tab.php',$args);?>
            </div>
        <?php }?>
    </div>

</div>
<?php

