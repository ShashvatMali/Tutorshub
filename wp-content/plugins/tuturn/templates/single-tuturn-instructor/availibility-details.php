<?php
global $post;
$time_day_key_arr   = apply_filters('tuturn_booking_time_of_day', '');
$time_of_day        = tuturnGetWeekDays();

$counter            = 0;
$content_row        = '';
$heading_row        = '';
if( !empty($time_day_key_arr) ){
    $heading_row        = '<thead><tr><th colspan="2"></th>';
    foreach($time_day_key_arr as $time_k =>$time_data){
        $counter++;
        $time_heading        = !empty($time_data['heading']) ? $time_data['heading'] : '';
        $icon                = !empty($time_data['icon']) ? $time_data['icon'] : '';
        $content_row        .= '<tr>';
        
        $content_row        .= '<th colspan="2" scope="row"> <span class="'.esc_attr($icon).'"></span>'.esc_attr($time_heading).'</th>';
        foreach($time_of_day as $d_key => $day){
            if( !empty($counter) && $counter === 1 ){
                $heading_row    .= '<th scope="col">'.esc_attr($day).'</th>';
            }
            
            $day_data   = get_post_meta($post->ID,$d_key,true );
            $day_data   = !empty($day_data) ? $day_data : array();
            if( !empty($day_data) && in_array($time_k,$day_data) ){
                $content_row    .= '<td><span class="fa fa-check"></span></td>';
            } else {
                $content_row    .= '<td class="tu-hasempty"> <div class="tu-nodata"><span></span></div> </td>';
            }
        }
        $content_row        .= '</tr>';
    }
    $heading_row    .= '</thead></tr>';
?>
    <div class="tu-tabswrapper">
        <div class="tu-tabstitle">
            <h4><?php esc_html_e('Availability','tuturn');?></h4>
        </div>
        <div class="table-responsive">
            <table class="table tu-availabletable "> 
                <?php echo do_shortcode($heading_row );?>
                <tbody><?php echo do_shortcode($content_row );?></tbody>
            </table>
        </div>
    </div>
<?php }
