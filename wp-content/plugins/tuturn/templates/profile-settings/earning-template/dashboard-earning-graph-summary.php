<?php
/**
 * The template part for displaying the dashboard earning graph summary for seller
 *
 * @package     Tuturn
 * @subpackage  Tuturn/templates/dashboard/earning_template
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
if (!class_exists('WooCommerce')) {
    return;
}

if (!empty($args) && is_array($args)) {
	extract($args);
}
$reference = !empty($reference) ? $reference : '';
$meta_array    = array(
    array(
        'key'         => 'instructor_id',
        'value'       => $user_identity,
        'compare'     => '=',
        'type'        => 'NUMERIC'
    ),
    array(
        'key'         => 'booking_status',
        'value'       => array('publish', 'completed'),
        'compare'     => 'IN',
    ),
    array(
        'key'         => 'payment_type',
        'value'       => 'booking',
        'compare'     => '=',
    )
);

$graph_date         = !empty($_GET['tu-earning-chart-date']) ? $_GET['tu-earning-chart-date'] : '';
$completed_earnings    = tuturn_instructor_earnings('shop_order', array('wc-completed'), $meta_array, $graph_date);
$graph_keys         = !empty($completed_earnings['key']) ? $completed_earnings['key'] : '';
$graph_values       = !empty($completed_earnings['values']) ? $completed_earnings['values'] : '';
$currency_symbol    = get_woocommerce_currency_symbol();
$profile_settings_url   = get_permalink();
if(!empty($user_identity)){
    $profile_settings_url   = add_query_arg(array('useridentity'=>$user_identity), $profile_settings_url);
} 
$current_page_link  = add_query_arg(array('tab'=>'earnings'), $profile_settings_url);
wp_enqueue_script('chart');
wp_enqueue_script('utils-chart');
?>
<div class="tu-dbwrapper">
    <div class="tu-dbtitle">
        <h3><?php esc_html_e('Earning history', 'tuturn'); ?></h3>
        <form id="earning-graph-search-form" action="<?php echo esc_url( $current_page_link ); ?>">
            <input type="hidden" name="useridentity" value="<?php echo esc_attr($user_identity); ?>">
            <input type="hidden" name="tab" value="earnings">
            <div class="tu-selectv">
                <select name="tu-earning-chart-date" id="tu-earnings-graph" data-placeholder="<?php esc_attr_e('Select month', 'tuturn'); ?>" class="form-control">
                    <option label="<?php esc_attr_e('Select month', 'tuturn'); ?>"></option>
                    <?php
                    $dt = strtotime(date('Y-m-01'));
                    for ($j = 5; $j >= 0; $j--) {
                        $date   = date("F Y", strtotime(" -$j month", $dt));
                        $date_month_year   = date("01-m-Y", strtotime(" -$j month", $dt));
                        $date       = date_i18n( 'F Y', strtotime( $date ) );
                        $selected   = '';
                        if($graph_date == $date_month_year){
                            $selected   = 'selected';
                        }
                        ?>
                            <option value="<?php echo esc_attr($date_month_year);?>" <?php echo esc_attr($selected);?>><?php echo esc_html($date);?></option>
                        <?php
                    }
                    ?>
                </select>
            </div>
        </form>
    </div>
    <div class="tu-barchart">
        <canvas id="tuturn-insightchart" height="400"></canvas>
    </div>
</div>
<?php
$script = "
jQuery(document).on('ready', function(){
    jQuery(document).on('change', '#tu-earnings-graph', function (e) {
        jQuery('#earning-graph-search-form').submit();
    });
});
window.addEventListener('load', (event) =>{
    var activity = document.getElementById('tuturn-insightchart');
    if (activity !== null) {
        activity.height = 400;
        var config = {
            type: 'line',
            data: {
            labels: [" . do_shortcode($graph_keys) . "],
            datasets: [{
                //pointBackgroundColor: window.chartColors.dark_blue,
                pointBackgroundColor: '#2f6fc4',
                backgroundColor: 'rgba(0,117,214,0.03)',
                borderColor: '#2f6fc4',
                borderWidth: 1,
                fill: true,
                pointBorderColor: '#ffffff',
                pointHoverBackgroundColor: '#fad85a',
                data: [" . do_shortcode($graph_values) . "],
            }]
            },
            options: {
                responsive: true,
                title:false,
                position: 'nearest',
                animation:{
                    duration:1000,
                    easing:'linear',
                    delay: 1500,
                },
                interaction: {
                    intersect: false,
                    mode: 'point',
                },
                font: {
                    family: 'Nunito'
                },
                plugins: {
                    filler: {
                        propagate: false,
                    },
                    tooltip: {
                        yAlign: 'bottom',
                        displayColors:false,
                        padding:{
                            x:15,
                            top:15,
                            bottom:9,
                        },
                        borderColor:'#eee',
                        borderWidth:1,
                        titleColor: '#353648',
                        bodyColor: '#353648',
                        bodySpacing: 6,
                        titleMarginBottom: 9,
                        backgroundColor:'rgba(255, 255, 255)',
                        callbacks: {
                            title: function(context){
                                return '" . esc_html__('Earning:', 'tuturn') . "'
                            },
                                label: function(context){
                                return '" . html_entity_decode($currency_symbol) . "' + context.dataset.data[context.dataIndex]
                            }
                        }
                    },
                    legend:{
                        display:false,
                    },
                },
                elements: {
                    line: {
                        tension: 0.000001
                    },
                },
                scales: {
                    y:{
                        ticks: {
                            fontSize: 12, fontFamily: '', fontColor: '#000', fontStyle: '500',
                            beginAtZero: true,
                            callback: function(value, index, values) {
                                if(parseInt(value) >= 1000){
                                    return '" . html_entity_decode($currency_symbol) . "' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                                } else {
                                    return '" . html_entity_decode($currency_symbol) . "' + value;
                                }
                            }
                        }
                    },
                    x:{
                        ticks: {fontSize: 12, fontFamily: '', fontColor: '#000', fontStyle: '500'},
                        grid:{
                            display : false
                        }
                    }
                },
            },
        }
        var ctx = document.getElementById('tuturn-insightchart').getContext('2d');

        var myLine = new Chart(ctx, config);
    };
})";
wp_add_inline_script('utils-chart', $script, 'after');
