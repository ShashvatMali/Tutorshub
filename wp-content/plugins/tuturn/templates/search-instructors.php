<?php

/**
 * Search instructors 
 *
 * @package     Tuturn
 * @subpackage  Tuturn/templates
 * @author      Amentotech <info@amentotech.com>
 * @link        https://themeforest.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
global $tuturn_settings, $wp;
get_header();
$teaching_preference = $product_cat_args = $tax_query_args = $meta_query_args = $meta_query_args2 = $price_range_meta_args = array();
$teach_settings                 = !empty($tuturn_settings['teach_settings']) ? $tuturn_settings['teach_settings'] : 'default';
$state_option                   = !empty($tuturn_settings['profile_state']) ? $tuturn_settings['profile_state'] : false;
$hide_price                     = !empty($tuturn_settings['hide_price']) ? $tuturn_settings['hide_price'] : false;
$hide_rating                    = !empty($tuturn_settings['hide_rating']) ? $tuturn_settings['hide_rating'] : false;
$tutur_availability             = isset($tuturn_settings['tutur_availability']) ? $tuturn_settings['tutur_availability'] : false;
$hide_tutor_location            = !empty($tuturn_settings['hide_tutor_location']) ? $tuturn_settings['hide_tutor_location'] : false;
$hide_miscellaneous             = !empty($tuturn_settings['hide_miscellaneous']) ? $tuturn_settings['hide_miscellaneous'] : false;
$profile_gender                 = !empty($tuturn_settings['profile_gender']) ? $tuturn_settings['profile_gender'] : false;

$exclude_uncategory             = !empty($tuturn_settings['hide_product_uncat'][0]) ? $tuturn_settings['hide_product_uncat'][0] : '';
$uncategorized_obj              = !empty($exclude_uncategory) ? get_term_by('slug', $exclude_uncategory, 'product_cat') : '';
$uncategorized_id               = !empty($uncategorized_obj) ? $uncategorized_obj->term_id : 0;

$pg_page                        = get_query_var('page') ? get_query_var('page') : 1; //rewrite the global var
$pg_paged                       = get_query_var('paged') ? get_query_var('paged') : 1;
$paged                             = max($pg_page, $pg_paged);
$product_cat                    = !empty($_GET['categories']) ? esc_html($_GET['categories']) : '';
$sub_categories                 = !empty($_GET['sub_categories']) ? $_GET['sub_categories'] : array();
$min_range                      = !empty($_GET['tu-min-range']) ? ($_GET['tu-min-range']) : 1;
$max_range                      = !empty($_GET['tu-max-range']) ? ($_GET['tu-max-range']) : 0;
$selected_gender                 = !empty($_GET['gender']) ? ($_GET['gender']) : 0;
$rating                         = !empty($_GET['rating']) ? $_GET['rating'] : array();
$google_api_key                 = !empty($tuturn_settings['google_map']) ? $tuturn_settings['google_map'] : '';
$search_keyword                 = !empty($_GET['keyword']) ? esc_html($_GET['keyword']) : '';
$myhome                         = !empty($_GET['myhome']) ? esc_html($_GET['myhome']) : '';
$available_days                 = !empty($_GET['available_days']) ? ($_GET['available_days']) : array();
$available_time                 = !empty($_GET['available_time']) ? ($_GET['available_time']) : array();
$studenthome                    = !empty($_GET['studenthome']) ? esc_html($_GET['studenthome']) : '';
$online_bookings                = !empty($_GET['online']) ? esc_html($_GET['online']) : '';
$location                       = !empty($_GET['location']) ? esc_html($_GET['location']) : '';
$Longitude                      = !empty($_GET['longitude']) ? esc_html($_GET['longitude']) : '';
$Latitude                       = !empty($_GET['latitude']) ? esc_html($_GET['latitude']) : '';
$radius                         = !empty($_GET['distance']) ? intval($_GET['distance']) : 2;
$distance                       = !empty($_GET['distance']) ? intval($_GET['distance']) : 2;
$sort_by                        = !empty($_GET['sort_by']) ? esc_attr($_GET['sort_by']) : 'desc';
$defult_search_location_type    = !empty($tuturn_settings['defult_search_location_type']) ? $tuturn_settings['defult_search_location_type'] : 'miles';
$search_template                = !empty($tuturn_settings['search_instructor_template']) ? $tuturn_settings['search_instructor_template'] : 'v1';
$search_template_view           = !empty($_GET['display']) ? esc_html($_GET['display']) : $search_template;
$instructor_search              = !empty($tuturn_settings['tpl_instructor_search']) ? get_permalink($tuturn_settings['tpl_instructor_search']) : '';
$identity_verification_listings = !empty($tuturn_settings['identity_verification_listings']) ?  $tuturn_settings['identity_verification_listings'] : '';
$serch_views                    = array('v1', 'v2', 'v3');


if (!empty($search_template_view) && !in_array($search_template_view, $serch_views)) {
    $search_template_view       = $search_template;
}

$current_page_url   = home_url(add_query_arg(array($_GET), $wp->request));
if (!empty($tuturn_settings['tpl_instructor_search'])) {
    $current_page_url               = add_query_arg(array($_GET), $instructor_search);
}

$listgender         = tuturn_get_gender_lists();
$grid_view_link                 = add_query_arg('display', 'v3', $current_page_url);

if (!empty($search_template) && $search_template != 'v3') {
    $list_view_link                 = add_query_arg('display', $search_template, $current_page_url);
} else if (!empty($search_template) && $search_template == 'v1') {
    $list_view_link                 = add_query_arg('display', 'v2', $current_page_url);
} else {
    $list_view_link                 = add_query_arg('display', 'v1', $current_page_url);
}

$meta_queries                   = array();
$meta_query_args                = array();
$base_url                       = get_the_permalink();
$current_url                    = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$per_page                       = !empty(get_option('posts_per_page')) ? get_option('posts_per_page') : 10;

if (!empty($defult_search_location_type) && $defult_search_location_type == 'km') {
    $radius   = $radius * 0.621371;
}

if (!empty($product_cat) && $product_cat != '-1') {
    $product_cat_args[] = array(
        'taxonomy' => 'product_cat',
        'field'    => 'slug',
        'terms'    => array($product_cat),
    );
    $tax_query_args[] = $product_cat_args;

    if (!empty($sub_categories) && is_array($sub_categories)) {
        $sub_categories    = array_map('esc_attr', $sub_categories);
        $query_relation = array('relation' => 'OR',);
        $type_args      = array();
        foreach ($sub_categories as $key => $type) {
            $type_args[] = array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => $type,
            );
        }
        $tax_query_args = array_merge($query_relation, $type_args);
    }
}

$country_region = !empty($_GET['country']) ? $_GET['country'] : '';
if (!empty($country_region)) {
    $meta_query_args[] = array(
        'key'           => '_country',
        'value'         => $country_region,
        'compare'       => 'LIKE'
    );
    if (!empty($state_option)) {
        $country_state  = !empty($_GET['state']) ? $_GET['state'] : '';
        $state_city     = !empty($_GET['city']) ? $_GET['city'] : '';
        $meta_query_args[] = array(
            'key'           => '_state',
            'value'         => $country_state,
            'compare'       => 'LIKE'
        );
        $meta_query_args[] = array(
            'key'           => '_city',
            'value'         => $state_city,
            'compare'       => 'LIKE'
        );
    }
}

$meta_query_args[] = array(
    'key'           => '_is_verified',
    'value'         => 'yes',
    'compare'       => '='
);

/* Rating */
if (!empty($rating)) {
    $meta_query_ragings = array();
    $rating    = array_map('absint', $rating);
    foreach ($rating as $key => $rate) {
        $meta_query_ragings['relation']  = 'OR';
        $meta_query_ragings[] = array(
            'key'       => 'tu_average_rating',
            'value'     => $rate,
            'compare'   => '<='
        );
    }

    $meta_query_args['tu_average_rating'] = $meta_query_ragings;
}

/* Location */
if (!empty($location) && !empty($google_api_key)) {
    if (!empty($defult_search_location_type) && $defult_search_location_type == 'km') {
        $radius = $radius * 0.621371;
    }

    if (empty($Latitude) && empty($Longitude)) {
        $args = array(
            'timeout'       => 15,
            'headers'       => array('Accept-Encoding' => ''),
            'sslverify'     => false
        );
        $url        = 'https://maps.google.com/maps/api/geocode/json?address=' . $location . '&key=' . $google_api_key;
        $response   = wp_remote_get($url, $args);
        $geocode    = wp_remote_retrieve_body($response);

        $output      = json_decode($geocode);

        if (isset($output->results) && !empty($output->results)) {
            $Latitude     = $output->results[0]->geometry->location->lat;
            $Longitude   = $output->results[0]->geometry->location->lng;
        }
    }

    if (!empty($Latitude) && !empty($Longitude)) {
        $zcdRadius      = new RadiusCheck($Latitude, $Longitude, $radius);
        $minLat         = $zcdRadius->MinLatitude();
        $maxLat         = $zcdRadius->MaxLatitude();
        $minLong        = $zcdRadius->MinLongitude();
        $maxLong        = $zcdRadius->MaxLongitude();
        $meta_query_args2[] = array(
            'relation' => 'AND',
            array(
                'key'           => '_latitude',
                'value'         => array($minLat, $maxLat),
                'compare'       => 'BETWEEN',
                'type'          => 'DECIMAL(20,10)',
            ),
            array(
                'key'           => '_longitude',
                'value'         => array($minLong, $maxLong),
                'compare'       => 'BETWEEN',
                'type'          => 'DECIMAL(20,10)',
            )
        );
    }
} elseif (!empty($location)) {
    $meta_query_args[] = array(
        'key'       => '_address',
        'value'     => $location,
        'compare'   => 'LIKE',
    );
}

// if min price field is set in search then append it in meta query
if (!empty($min_range) && !empty($max_range) && empty($disable_range_slider)) {
    $price_range_meta_args[] = array(
        'key'       => 'hourly_rate',
        'value'     =>  array($min_range, $max_range),
        'type'      => 'numeric',
        'compare'   => 'between'

    );

    // store basic taxonomy in $tax_queries array
    $meta_query_args = array_merge($meta_query_args, $price_range_meta_args);
}

if (!empty($identity_verification_listings)) {
    $meta_query_args[]  = array(
        'key'       => 'identity_verified',
        'value'     => 'yes',
        'compare'   => '=',
    );
}

if (!empty($selected_gender) && $selected_gender !== 'both') {
    $meta_query_args[]  = array(
        'key'       => '_gender',
        'value'     => $selected_gender,
        'compare'   => '=',
    );
}

if (!empty($myhome)) {
    $teaching_preference[]  = array(
        'key'       => 'teaching_preference',
        'value'     => 'home',
        'compare'   => 'LIKE',
    );
}

if (!empty($studenthome)) {
    $teaching_preference[]  = array(
        'key'       => 'teaching_preference',
        'value'     => 'student_home',
        'compare'   => 'LIKE',
    );
}

if (!empty($online_bookings)) {
    $teaching_preference[]  = array(
        'key'       => 'teaching_preference',
        'value'     => 'online',
        'compare'   => 'LIKE',
    );
}

if (!empty($teach_settings) && $teach_settings === 'custom') {
    $teaching_type          = !empty($_GET['teaching_type']) ? ($_GET['teaching_type']) : '';
    if (!empty($teaching_type)) {
        foreach ($teaching_type as $teaching_val) {
            $teaching_preference[]  = array(
                'key'       => 'teaching_preference',
                'value'     => serialize(strval($teaching_val)),
                'compare'   => 'LIKE',
            );
        }
    }

    $offline_type          = !empty($_GET['offline_type']) ? ($_GET['offline_type']) : '';
    if (!empty($teaching_type) && in_array('offline', $teaching_type) && !empty($offline_type)) {
        foreach ($offline_type as $offline_type_val) {
            $teaching_preference[]  = array(
                'key'       => 'offline_place',
                'value'     => serialize(strval($offline_type_val)),
                'compare'   => '=',
            );
        }
        if (!empty($offline_type) && in_array('tutor', $offline_type)) {
            $tutor_location     = !empty($_GET['tutor_location']) ? esc_html($_GET['tutor_location']) : '';
            $tutor_latitude     = !empty($_GET['tutor_latitude']) ? $_GET['tutor_latitude'] : '';
            $tutor_longitude    = !empty($_GET['tutor_longitude']) ? $_GET['tutor_longitude'] : '';
            $tutor_distance     = !empty($_GET['tutor_distance']) ? $_GET['tutor_distance'] : '';
            $tutor_country      = !empty($_GET['tutor_country']) ? $_GET['tutor_country'] : '';
            if (!empty($tutor_country)) {
                $tutor_state        = !empty($_GET['tutor_state']) ? $_GET['tutor_state'] : '';
                $tutor_city         = !empty($_GET['tutor_city']) ? $_GET['tutor_city'] : '';
                $meta_query_args[] = array(
                    'key'         => '_tutor_country',
                    'value'     => $tutor_country,
                    'compare'     => '='
                );
                if (!empty($tutor_state)) {
                    $meta_query_args[] = array(
                        'key'         => '_tutor_state',
                        'value'     => $tutor_state,
                        'compare'     => '='
                    );
                }
                if (!empty($tutor_city)) {
                    $meta_query_args[] = array(
                        'key'         => '_tutor_city',
                        'value'     => $tutor_city,
                        'compare'     => '='
                    );
                }
            }

            if (!empty($tutor_location) && !empty($google_api_key)) {
                if ($defult_search_location_type == 'km') {
                    $tutor_distance = $tutor_distance * 0.621371;
                }

                if (empty($tutor_latitude) && empty($tutor_longitude)) {
                    $args = array(
                        'timeout'     => 15,
                        'headers' => array('Accept-Encoding' => ''),
                        'sslverify' => false
                    );
                    $url        = 'https://maps.google.com/maps/api/geocode/json?address=' . $tutor_location . '&key=' . $google_api_key;
                    $response   = wp_remote_get($url, $args);
                    $geocode    = wp_remote_retrieve_body($response);

                    $output      = json_decode($geocode);

                    if (isset($output->results) && !empty($output->results)) {
                        $tutor_latitude     = $output->results[0]->geometry->location->lat;
                        $tutor_longitude   = $output->results[0]->geometry->location->lng;
                    }
                }

                if (!empty($tutor_latitude) && !empty($tutor_longitude)) {
                    $zcdRadius  = new RadiusCheck($tutor_latitude, $tutor_longitude, $tutor_distance);
                    $minLat     = $zcdRadius->MinLatitude();
                    $maxLat     = $zcdRadius->MaxLatitude();
                    $minLong     = $zcdRadius->MinLongitude();
                    $maxLong     = $zcdRadius->MaxLongitude();
                    $meta_query_args2[] = array(
                        'relation' => 'AND',
                        array(
                            'key'         => '_tutor_latitude',
                            'value'     => array($minLat, $maxLat),
                            'compare'     => 'BETWEEN',
                            'type'         => 'DECIMAL(20,10)',
                        ),
                        array(
                            'key'         => '_tutor_longitude',
                            'value'     => array($minLong, $maxLong),
                            'compare'     => 'BETWEEN',
                            'type'         => 'DECIMAL(20,10)',
                        )
                    );
                }
            } elseif (!empty($tutor_location)) {
                $meta_query_args[] = array(
                    'key'       => '_tutor_address',
                    'value'     => $tutor_location,
                    'compare'   => 'LIKE',
                );
            }
        }
    }
}

$query_args = array(
    'posts_per_page'        => $per_page,
    'paged'                 => $paged,
    'post_type'             => 'tuturn-instructor',
    'post_status'           => 'publish',
    'ignore_sticky_posts'   => 1,
);

if (!empty($sort_by)) {
    if ($sort_by == 'asc') {
        $sort_by_args = array(
            'meta_key'      => 'hourly_rate',
            'orderby' 		=> array('meta_value_num' => 'ASC', 'title' => 'ASC'),
        );
    } elseif ($sort_by == 'desc') {
        $sort_by_args = array(
            'meta_key'      => 'hourly_rate',
            'orderby' 		=> array('meta_value_num' => 'DESC', 'title' => 'ASC'),
        );
    }
    if (!empty($sort_by_args)) {
        $query_args = array_merge($query_args, $sort_by_args);
    }
} else {
    $query_args['orderby'] = 'date';
    $query_args['order'] = 'DESC';
}

// if keyword field is set in search then append its args in $query_args
if (!empty($search_keyword)) {
    add_filter('posts_where', 'tuturn_advance_search_where_instructors');
    add_filter('posts_join', 'tuturn_advance_search_join');
    add_filter('posts_groupby', 'tuturn_advance_search_groupby');
}
if (!empty($available_time)) {
    add_filter('posts_where', 'tuturn_advance_search_available_time');
    if (empty($search_keyword)) {
        add_filter('posts_join', 'tuturn_advance_search_join');
    }
}
//Taxonomy Query
if (!empty($tax_query_args)) {
    $query_relation = array('relation' => 'AND',);
    $query_args['tax_query'] = array_merge($query_relation, $tax_query_args);
}
$updated_query_day = array();
if (!empty($available_days)) {
    foreach ($available_days as $available_day) {
        $meta_query_day[] = array(
            'key'         => $available_day,
            'compare'     => 'EXISTS'
        );
    }
    $query_relation_day         = array('relation' => 'OR',);
    $meta_query_day             = array_merge($query_relation_day, $meta_query_day);
    $updated_query_day[]        = $meta_query_day;
}


//Meta Query
if (!empty($meta_query_args)) {

    if (!empty($meta_query_args2)) {
        $meta_query_args         = array_merge($meta_query_args2, $meta_query_args);
    }

    $query_relation           = array('relation' => 'AND',);
    $meta_query_args          = array_merge($query_relation, $meta_query_args);
    if (!empty($updated_query_day)) {
        $meta_query_args          = array_merge($meta_query_args, $updated_query_day);
    }
    if (!empty($teaching_preference)) {
        $tech_option                        = array('relation' => 'OR',);
        $teaching_preference_data[]         = array_merge($tech_option, $teaching_preference);
        if (!empty($teaching_preference_data)) {
            $meta_query_args         = array_merge($teaching_preference_data, $meta_query_args);
        }
    }

    $query_args['meta_query'] = $meta_query_args;
}

$instructors_data = new WP_Query(apply_filters('tuturn_instructor_search_filter', $query_args));

// remove filters
if (!empty($search_keyword)) {
    remove_filter('posts_where', 'tuturn_advance_search_where_instructors');
    remove_filter('posts_join', 'tuturn_advance_search_join');
    remove_filter('posts_groupby', 'tuturn_advance_search_groupby');
}
$total_posts = $instructors_data->found_posts;
?>
<section class="tu-main-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="tu-listing-wrapper">
                    <div class="tu-sort">
                        <h3><?php echo sprintf(_n('%s search result found', '%s search results found', $total_posts, 'tuturn'), $total_posts); ?></h3>
                        <div class="tu-sort-right-area">
                            <?php do_action('instructor_search_sort', $sort_by); ?>
                            <div div class="tu-filter-btn">
                                <a class="tu-listbtn<?php if ($search_template_view !== 'v3') {
                                                        echo esc_attr(' active');
                                                    } ?>" href="<?php echo esc_url($list_view_link); ?>"><i class="icon icon-list"></i></a>
                                <a class="tu-listbtn<?php if ($search_template_view == 'v3') {
                                                        echo esc_attr(' active');
                                                    } ?>" href="<?php echo esc_url($grid_view_link); ?>"><i class="icon icon-grid"></i></a>
                            </div>
                        </div>
                        <div class="tu-searchbar-wrapper">
                            <div class="tu-appendinput">
                                <form id="tu-instructor-keyword-search-form" action="<?php echo esc_url($instructor_search) ?>" method="GET" class="tu-searcbar">
                                    <div class="tu-inputicon">
                                        <a href="javascript:void(0);"><i class="icon icon-search"></i></a>
                                        <input name="keyword" type="text" class="form-control" placeholder="<?php esc_attr_e('What are you looking for?', 'tuturn') ?>" value="<?php echo esc_attr($search_keyword) ?>">
                                    </div>
                                    <div class="tu-select">
                                        <i class="icon icon-layers"></i>
                                        <?php
                                        if (class_exists('WooCommerce')) {
                                            $tax_args = array(
                                                'show_option_none'  => esc_html__('Select category', 'tuturn'),
                                                'show_count'        => false,
                                                'hide_empty'        => 0,
                                                'name'              => 'categories',
                                                'class'             => 'form-control tu-input-field tu-select-category',
                                                'taxonomy'          => 'product_cat',
                                                'id'                => 'parent-category-search-dropdown',
                                                'value_field'       => 'slug',
                                                'orderby'           => 'name',
                                                'selected'          => $product_cat,
                                                'hide_if_empty'     => true,
                                                'echo'              => true,
                                                'parent'            => 0,
                                                'required'          => false,
                                                'disabled'             => true,
                                            );
                                            if (!empty($uncategorized_id)) {
                                                $tax_args['exclude'] = array($uncategorized_id);
                                            }
                                            wp_dropdown_categories($tax_args);
                                        }
                                        ?>
                                    </div>
                                    <a href="javascript:void(0);" class="tu-primbtn-lg tu-primbtn-orange" id="tu-instructor-search-keyword"><?php esc_html_e('Search now', 'tuturn') ?></a>
                                </form>
                            </div>
                            <div class="tu-listing-search">
                                <figure>
                                    <img src="<?php echo esc_url(TUTURN_DIRECTORY_URI . 'public/images/shape.png') ?>" alt="<?php esc_attr_e('image', 'tuturn') ?>">
                                </figure>
                                <span><?php esc_html_e('Start from here', 'tuturn') ?> </span>
                            </div>
                        </div>
                        <ul class="tu-searchtags">
                            <?php if (!empty($sub_categories)) {
                                foreach ($sub_categories as $index => $value) {
                                    $sub_cat        = get_term_by('slug', $value, 'product_cat');
                                    $sub_cat_name   = !empty($sub_cat) ? esc_html($sub_cat->name) : '' ?>
                                    <li id="sub-category-<?php echo esc_html($index) ?>">
                                        <span><?php echo esc_html($sub_cat_name) ?><a href="javascript:void(0)" class="remove-subcategory" data-value="<?php echo esc_attr($value) ?>" data-position="<?php echo esc_attr($index) ?>">
                                                <i class="icon icon-x"></i></a></span>
                                    </li>
                            <?php }
                            } ?>
                        </ul>
                        <?php if (!empty($_GET['sub_categories']) || !empty($_GET['keyword'])) { ?>
                            <a href="<?php the_permalink(); ?>" class="tu-sb-sliver"><?php esc_html_e('Clear all filters', 'tuturn'); ?></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-xxl-3">
                <aside class="tu-asidewrapper">
                    <a href="javascript:void(0)" class="tu-dbmenu"><i class="icon icon-chevron-left"></i></a>
                    <div class="tu-dbnavlist">
                        <form id="tu-instructor-search" action="<?php echo esc_url($instructor_search) ?>" method="GET">
                            <?php do_action('tuturn_categories_search', array('category' => $product_cat, 'sub_categories' => $sub_categories)); ?>
                            <?php if (!empty($hide_price)) {
                                do_action('tuturn_price_range', array('max-range' => $max_range, 'min-range' => $min_range));
                            } ?>
                            <?php if (!empty($profile_gender)) { ?>
                                <div class="tu-aside-holder">
                                    <div class="tu-asidetitle" data-bs-toggle="collapse" data-bs-target="#tu-gender-content" role="button" aria-expanded="true">
                                        <h5><?php esc_html_e('Gender', 'tuturn'); ?></h5>
                                    </div>
                                    <div id="tu-gender-content" class="collapse show">
                                        <div class="tu-aside-content">
                                            <div class="tu-select">
                                                <select class="form-control" name="gender" data-placeholder="<?php esc_attr_e('Both', 'tuturn'); ?>" required>
                                                    <option value="both"><?php esc_html_e('Both', 'tuturn'); ?></option>
                                                    <?php foreach ($listgender as $key => $value) { ?>
                                                        <option <?php if (strtolower($selected_gender) == strtolower($key)) {
                                                                    echo esc_attr('selected');
                                                                } ?> value="<?php echo esc_attr(strtolower($key)) ?>"><?php echo esc_html($value) ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if (!empty($tutur_availability)) {
                                do_action('tuturn_tutur_availability', array('rating' => $rating));
                            } ?>
                            <?php if (!empty($hide_rating)) {
                                do_action('tuturn_rating_search', array('rating' => $rating));
                            } ?>
                            <?php if (!empty($hide_tutor_location)) {
                                do_action('tuturn_location_search', array('location' => $location, 'distance' => $distance, 'latitude' => $Latitude, 'longitude' => $Longitude));
                            } ?>
                            <?php if (!empty($hide_miscellaneous)) {
                                do_action('tuturn_miscellaneous_search', array('online_bookings' => $online_bookings, 'studenthome' => $studenthome, 'myhome' => $myhome, 'distance' => $distance));
                            } ?>
                            <div class="tu-filterbtns">
                                <a href="javascript:void(0);" id="tu_search_instructor_filter" class="tu-primbtn"><?php esc_html_e('Apply filters', 'tuturn'); ?></a>
                                <a href="<?php the_permalink(); ?>" class="tu-sb-sliver"><?php esc_html_e('Clear all filters', 'tuturn'); ?></a>
                            </div>
                        </form>
                    </div>
                </aside>
            </div>
            <div class="col-xl-8 col-xxl-9">
                <div class="tu-listinginfo-holder">
                    <?php if ($instructors_data->have_posts()) {

                        if (!empty($search_template_view) && $search_template_view == 'v3') { ?>
                            <div class="row gy-4">
                                <?php
                            }

                            while ($instructors_data->have_posts()) {
                                $instructors_data->the_post();
                                global $post;
                                if (!empty($search_template_view) && $search_template_view !== 'v3') {
                                    tuturn_get_template_part('instructor-search/instructor-search-view-' . $search_template_view);
                                } else { ?>
                                    <div class="col-md-6 col-xxl-4">
                                        <?php tuturn_get_template_part('instructor-search/instructor-search-view-v4'); ?>
                                    </div>
                                <?php
                                }
                            }
                            if (!empty($search_template_view) && $search_template_view == 'v3') { ?>
                            </div>
                    <?php
                            }

                            if (!empty($total_posts)) {
                                tuturn_paginate($instructors_data, 'tu-pagination');
                            }
                        } else {
                            do_action('tuturn_instructors_empty_record');
                        } ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
get_footer();
