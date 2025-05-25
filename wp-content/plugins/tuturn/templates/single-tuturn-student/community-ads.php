<?php
/**
 *
 * Sudent community ads
 *
 * @package     Tuturn
 * @subpackage  Tuturn/Templates/Single_tuturn_Instructor
 * @author      Amentotech <info@amentotech.com>
 * @link       https://themeforest.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/
global $tuturn_settings;
$ads_html   = !empty( $tuturn_settings['ads_html'] ) ? $tuturn_settings['ads_html'] : '';
if(!empty($ads_html)){?>
    <div class="tu-Joincommunity">
        <?php echo do_shortcode($ads_html);?>
    </div>
    <?php
    $community_ads_particles = '
    // particals
    var tu_particle = document.getElementById("tu-particle");
    if (tu_particle !== null) {
        /* ---- particles.js config ---- */
        particlesJS("tu-particle", {
            "particles": {
                "number": {
                "value": 40,
                },
                "color": {
                "value": "#ffffff"
                },
                "opacity": {
                "value": 0.4,
                "random": false,
                
                },
                size: {
                value: 12,
                random: true,
                },
                "line_linked": {
                "enable": false,
                },
                "move": {
                "enable": true,
                "speed": 3,
                }
            },
            "interactivity": {
                "enable": false,
                "detect_on": "canvas",
                "events": {
                "onhover": {
                    "enable": false,
                    "mode": "grab"
                },
                "onclick": {
                    "enable": false,
                    "mode": "push"
                },
                },
            },
        });
    }';
    wp_add_inline_script('particles', $community_ads_particles, 'after');
}
