<?php
/**
 * Instructor education details
 *
 * @package     Tuturn
 * @subpackage  Tuturn/Templates/Profile_Settings/Instructor
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/
global $tuturn_settings;
$eductation_date_option = !empty($tuturn_settings['eductation_date_option']) ? $tuturn_settings['eductation_date_option'] : false;
$education_listings     = !empty($profile_details['education']) ? $profile_details['education'] : array();
?>
<div class="tu-boxarea">
        <div class="tu-boxsm">
            <div class="tu-boxsmtitle"> 
                <h4><?php esc_html_e('Education', 'tuturn');?></h4>
                <a href="javascript:void(0);" class="tu_edit_education" data-operation="add" ><?php esc_html_e('Add education', 'tuturn'); ?></a>
            </div>
        </div>
        <div class="tu-box">
            <div class="accordion tu-accordionedu" id="accordionFlushExampleaa">
                <div id="tu-edusortable" class="tu-edusortable ">
                    <?php if (isset($education_listings) && is_array($education_listings) && count($education_listings) > 0) {
                        foreach ($education_listings as $key => $education) { ?>
                            <div class="tu-accordion-item tu-education-item-<?php echo esc_attr($key);?>">
                                <div class="tu-expwrapper">
                                    <div class="tu-accordionedu">
                                        <div class="tu-expinfo">
                                            <div class="tu-accodion-holder">
                                                <h4 class="collapsed" data-bs-toggle="collapse" data-bs-target="#flush-collapseOneba<?php echo esc_attr($key);?>" aria-expanded="true" aria-controls="flush-collapseOneba"><?php echo esc_html($education['degree_title']); ?></h4>
                                                <ul class="tu-branchdetail">
                                                    <li><i class="icon icon-home"></i><span><?php echo esc_html($education['institute_title']); ?></span></li>
                                                    <li><i class="icon icon-map-pin"></i><span><?php echo esc_html($education['institute_location']); ?></span></li>
                                                    <?php if(!empty($education['education_degree_desc'])){?>
                                                        <li><i class="icon icon-calendar"></i><span><?php echo esc_html($education['education_degree_desc']); ?></span></li>
                                                    <?php } else {?>   
                                                        <li>
                                                            <i class="icon icon-calendar"></i>
                                                            <span>
                                                                <?php echo date_i18n('F Y', strtotime($education['education_start_date']));?>
                                                                - 
                                                                <?php if(!empty($education['currently_studying']) && $education['currently_studying'] == 'on'){
                                                                    esc_html_e('Present', 'tuturn');
                                                                } else {
                                                                    echo date_i18n('F Y', strtotime($education['education_end_date']));
                                                                }?>
                                                            </span>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                            <div class="tu-icon-holder">
                                                <a href="#" data-education_key="<?php echo esc_attr($key); ?>" class="tu-education-delete"><i class="icon icon-edit icon icon-trash-2 tu-deleteclr" role="button"></i></a>
                                                <a href="#" data-education='<?php echo json_encode($education); ?>' data-operation="edit" data-education_key="<?php echo esc_attr($key); ?>" class="tu_edit_education"><i class="icon icon-delete icon icon-edit-3 tu-editclr" role="button"></i></a>
                                            </div>
                                            <i class="icon icon-plus" role="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOneba<?php echo esc_attr($key);?>" aria-expanded="false" aria-controls="flush-collapseOneba"></i>
                                        </div>
                                    </div>
                                </div>
                                <div id="flush-collapseOneba<?php echo esc_attr($key);?>" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExampleaa">
                                    <div class="tu-edubodymain">
                                        <div class="tu-accordioneduc">
                                            <p><?php echo esc_html($education['education_description']); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="education[<?php echo esc_attr($key);?>]" value='<?php echo json_encode($education); ?>' >
                            </div>
                            <?php
                        }
                    } ?>
                </div>
            </div>
        </div>
</div>
<script type="text/template" id="tmpl-load-education">
    <div class="tu-expwrapper">
        <div class="tu-accordionedu">
            <div class="tu-expinfo">
                <div class="tu-accodion-holder">
                    <h4 class="collapsed" data-bs-toggle="collapse" data-bs-target="#flush-collapseOneba{{data.id}}" aria-expanded="false" aria-controls="flush-collapseOneba">{{data.degree_title}}</h4>
                    <ul class="tu-branchdetail">
                        <li><i class="icon icon-home"></i><span>{{data.institute_title}}</span></li>
                        <li><i class="icon icon-map-pin"></i><span>{{data.institute_location}}</span></li>
                        <# if(data.education_degree_desc !== ''){ #>
                            <li><i class="icon icon-calendar"></i><span>{{data.education_degree_desc}}</span></li>
                        <# }#>
                        <# if(data.education_degree_desc == ''){ #>
                            <li>
                                <i class="icon icon-calendar"></i>
                                <span>
                                    {{data.education_start_date}}
                                        - 
                                    <# if(data.currently_studying == 'on'){ #>
                                        <?php esc_html_e('Present', 'tuturn') ?>
                                    <# }#>

                                    <# if(data.currently_studying == 'off'){ #>
                                        {{data.education_end_date}}
                                    <# }#>
                                </span>
                            </li>
                        <# }#>
                    </ul>
                </div>
                <div class="tu-icon-holder">
                    <a href="#" data-education_key="{{data.id}}" class="tu-education-delete"><i class="icon icon-edit icon icon-trash-2 tu-deleteclr" role="button"></i></a>
                    <a href="#" data-education_key="{{data.id}}" data-operation="edit" data-education='{{JSON.stringify(data)}}' class="tu_edit_education"><i class="icon icon-edit-3 tu-editclr" role="button"></i></a>
                </div>
                <i class="icon icon-plus" role="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOneba{{data.id}}" aria-expanded="false" aria-controls="flush-collapseOneba"></i>
            </div>
        </div>
    </div>
    <div id="flush-collapseOneba{{data.id}}" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExampleaa">
        <div class="tu-edubodymain">
            <div class="tu-accordioneduc">
                <p>{{data.education_description}}</p>
            </div>
        </div>
    </div>
    <input type="hidden" id="education_{{data.id}}" name="education[{{data.id}}]" value='{{JSON.stringify(data)}}' >    
</script>
<script type="text/template" id="tmpl-load-education-popup">
    <div class="tu-popuptitle"> 
        <h4><?php esc_html_e('Add new education', 'tuturn'); ?></h4>
        <a href="javascript:void(0);" class="close"><i class="icon icon-x" data-bs-dismiss="modal"></i></a>
    </div>
    <div class="modal-body">    
        <form class="tu-themeform">
            <fieldset>
                <input type="hidden" id="education_key" value="{{data.id}}">
                <div class="tu-themeform__wrap">
                    <div class="form-group">
                        <label class="tu-label"><?php esc_html_e('Degree/course title', 'tuturn'); ?></label>
                        <div class="tu-placeholderholder">
                            <input type="text" id="education_degree_title" name="education[{{data.id}}][degree_title]" value="{{data.degree_title}}" class="form-control tu-form-input" placeholder=" " required>
                            <div class="tu-placeholder">
                                <span><?php esc_html_e('Enter title here', 'tuturn'); ?></span>
                                <em>*</em>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="tu-label"><?php esc_html_e('University/Institute title', 'tuturn'); ?></label>
                        <div class="tu-placeholderholder">
                            <input type="text" id="education_institute_title" name="education[{{data.id}}][institute_title]" value="{{data.institute_title}}" placeholder=" " class="form-control tu-form-input" required>
                            <div class="tu-placeholder">
                                <span><?php esc_html_e('Enter title here', 'tuturn'); ?></span>
                                <em>*</em>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="tu-label"><?php esc_html_e('Loaction', 'tuturn'); ?></label>
                        <div class="tu-placeholderholder">
                            <input type="text" id="education_institute_location" name="education[{{data.id}}][institute_location]" placeholder=" " value="{{data.institute_location}}" class="form-control tu-form-input" required>
                            <div class="tu-placeholder">
                                <span><?php esc_html_e('Enter location', 'tuturn'); ?></span>
                                <em>*</em>
                            </div>
                        </div>
                    </div>
                    
                    <?php if(empty($eductation_date_option)){?>
                    <div class="form-group-wrap">
                        <div class="form-group pb-0">
                            <label class="tu-label"><?php esc_html_e('Start & end date', 'tuturn'); ?></label>
                        </div>
                        <div class="form-group form-group-half">
                            <div class="tu-placeholderholder">
                                <div class="tu-calendar">
                                    <input type="text" id="tu_education_start_date" placeholder=" " name="education[{{data.id}}][education_start_date]" value="{{data.education_start_date}}" class="tu-start-date tu-datepicker form-control tu-form-input" required>
                                    <div class="tu-placeholder">
                                        <span><?php esc_html_e('Enter start date', 'tuturn'); ?></span>
                                        <em>*</em>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-half">
                            <div class="tu-placeholderholder">
                                <div class="tu-calendar">
                                    <input type="text" id="tu_education_end_date" placeholder=" " name="education[{{data.id}}][education_end_date]" value="{{data.education_end_date}}" class="tu-end-date tu-datepicker form-control tu-form-input {{ data.currently_studying == 'on' ? 'd-none' : '' }}" required>
                                    <input type="hidden" id="edu_deg_dis_able" name="education[{{data.id}}][edu_deg_dis_able]" placeholder=" " value="<?php echo esc_attr($eductation_date_option);?>" class="form-control tu-form-input" required>
                                    <div class="tu-placeholder">
                                        <span><?php esc_html_e('Enter end date', 'tuturn'); ?></span>
                                        <em>*</em>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group pt-0">
                            <div class="tu-check">
                                <input type="checkbox" class="tu_currently_studying" placeholder=" "  id="tu_currently_studying" name="education[{{data.id}}][currently_studying]" {{ data.currently_studying == 'on' ? 'checked' : '' }}>
                                <label for="tu_currently_studying"><?php esc_html_e('This degree/course is currently ongoing', 'tuturn'); ?></label>
                            </div>
                        </div>
                    </div> 
                    <?php } ?>
                    <?php if(!empty($eductation_date_option)){?>
                        <div class="form-group">
                            <label class="tu-label"><?php esc_html_e('Degree duration description', 'tuturn'); ?></label>
                            <div class="tu-placeholderholder">
                            <input type="text" id="education_degree_desc" name="education[{{data.id}}][degree_desc]" placeholder=" " value="{{data.education_degree_desc}}" class="form-control tu-form-input" required>
                                <input type="hidden" id="edu_deg_dis_able" name="education[{{data.id}}][edu_deg_dis_able]" placeholder=" " value="<?php echo esc_attr($eductation_date_option);?>" class="form-control tu-form-input" required>
                                <div class="tu-placeholder">
                                    <span><?php esc_html_e('Enter degree description', 'tuturn'); ?></span>
                                    <em>*</em>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="form-group tu-message-text">
                        <label class="tu-label"><?php esc_html_e('Description', 'tuturn');?></label>
                        <div class="tu-placeholderholder">
                        <textarea class="form-control tu-form-input tu-education-description-value" id="tu-education-description" name="education[{{data.id}}][education_description]" required placeholder="<?php esc_attr_e('Enter description', 'tuturn'); ?>"  maxlength="500">{{data.education_description}}</textarea>
                        </div>
                        <div class="tu-input-counter">
                            <span><?php esc_html_e('Characters left', 'tuturn');?>:</span>
                            <b class="tu_current_comment"><?php echo intval(500);?></b>
                            /
                            <em class="tu_maximum_comment"> <?php echo intval(500);?></em>
                        </div>
                    </div>    
                    <div class="form-group tu-formbtn">                                         
                        <a href="javascript:void(0);" class="tu-primbtn-lg" id="tu-submit-education"> <?php esc_html_e('Save & update changes', 'tuturn'); ?></a>
                    </div>
            </fieldset>
        </form> 
    </div> 
</script>