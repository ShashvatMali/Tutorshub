<?php 
/**
 * Instructor education details
 *
 * @package     Tuturn
 * @subpackage  Tuturn/Templates/Single_tuturn_Instructor
 * @author      Amentotech <info@amentotech.com>
 * @link       https://themeforest.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/

global $tuturn_settings;
$education_listings    = !empty($profile_details['education']) ? $profile_details['education'] : array();
if (isset($education_listings) && is_array($education_listings) && count($education_listings) > 0) { ?>
    <div class="tu-tabswrapper">
        <div class="tu-tabstitle">
            <h4><?php esc_html_e('Education', 'tuturn'); ?></h4>
        </div>
        <div class="accordion tu-accordionedu" id="accordionFlushExampleaa">
            <div id="tu-edusortable" class="tu-edusortable">
                <?php
                $counter = 0;
                foreach ($education_listings as $key => $education) {
                    $counter++;
                    $first_element_class    = '';
                    $area_expanded    = 'false';
                    if ($counter == 1) {
                        $first_element_class    = ' show';
                        $area_expanded    = 'true';
                    } ?>
                    <div class="tu-accordion-item">
                        <div class="tu-expwrapper">
                            <div class="tu-accordionedu">
                                <div class="tu-expinfo">
                                    <div class="tu-accodion-holder">
                                        <h4 class="collapsed" data-bs-toggle="collapse" data-bs-target="#flush-collapseOneba<?php echo esc_attr($key); ?>" aria-expanded="<?php echo esc_attr($area_expanded); ?>" aria-controls="flush-collapseOneba"><?php echo esc_html($education['degree_title']); ?></h4>
                                        <ul class="tu-branchdetail">
                                            <li><i class="icon icon-home"></i><span><?php echo esc_html($education['institute_title']); ?></span></li>
                                            <li><i class="icon icon-map-pin"></i><span><?php echo esc_html($education['institute_location']); ?></span></li>
                                            <?php if(!empty($education['education_degree_desc'])){?>
                                                <li><i class="icon icon-calendar"></i><span><?php echo esc_html($education['education_degree_desc']); ?></span></li>
                                            <?php } else {?>  
                                                <li>
                                                    <i class="icon icon-calendar"></i>
                                                    <span>
                                                        <?php echo date_i18n('F Y', strtotime($education['education_start_date'])); ?>
                                                        -
                                                        <?php if (!empty($education['currently_studying']) && $education['currently_studying'] == 'on') {
                                                            esc_html_e('Present', 'tuturn');
                                                        } else {
                                                            echo date_i18n('F Y', strtotime($education['education_end_date']));
                                                        } ?>

                                                    </span>
                                                </li> 
                                            <?php } ?>
                                        </ul>
                                    </div>
                                    <i class="icon icon-plus" role="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOneba<?php echo esc_attr($key); ?>" aria-expanded="<?php echo esc_attr($area_expanded); ?>" aria-controls="flush-collapseOneba"></i>
                                </div>
                            </div>
                        </div>
                        <div id="flush-collapseOneba<?php echo esc_attr($key); ?>" class="accordion-collapse collapse<?php echo esc_attr($first_element_class); ?>" data-bs-parent="#accordionFlushExampleaa">
                            <div class="tu-edubodymain">
                                <div class="tu-accordioneduc">
                                    <?php echo apply_filters('the_contnet', $education['education_description']); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?php }
