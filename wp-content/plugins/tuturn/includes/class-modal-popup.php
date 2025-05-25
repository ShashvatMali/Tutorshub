<?php
namespace thickboxmodel;
// die if accessed directly
if (!defined('ABSPATH')) {
    die('no kiddies please!');
}

/**
 *
 * Class 'Tuturn_Modal_Popup' defines the bootstrap modal
 *
 * @package     Tuturn
 * @subpackage  Tuturn/includes
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */

if (!class_exists('Tuturn_Modal_Popup')) {

    class Tuturn_Modal_Popup
    {

        public function __construct()
        {
            add_action('wp_footer', array($this, 'tuturn_prepare_modal_popup'));
        }

        /**
         * Task add-ons popup
         *
         * @return
         * @throws error
         * @author Amentotech <theamentotech@gmail.com>
         */
        public function tuturn_prepare_modal_popup()
        {
            ob_start();?>
            <div class="modal fade tuturn-profilepopup tu-uploadprofile tuturn-popup" tabindex="-1" role="dialog" id="tuturn-modal-popup">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="tuturn-modalcontent modal-content">
                        <div id="tuturn-model-body"></div>
                    </div>
                </div>
            </div>
            <?php
            echo ob_get_clean();
        }
    }
}

new Tuturn_Modal_Popup();
