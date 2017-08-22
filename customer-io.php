<?php
/*
 * Plugin Name: Customer.io Gravity Forms Add-on
 * Plugin URI: https://smartcatdesign.net/
 * Description: Easily add customers to customer.io when users submit a Gravity Form
 * Version: 1.0.0
 * Author: Smartcat
 * Author URI: https://smartcatdesign.net
 * License: GPL2
*/

namespace cio;


if ( ! defined( 'ABSPATH' ) ) {
    die;
}


include_once 'constants.php';


function init() {

    if ( class_exists( '\GFAPI' ) ) {

        include_once dirname( __FILE__ ) . '/includes/functions.php';
        include_once dirname( __FILE__ ) . '/includes/helpers.php';
        include_once dirname( __FILE__ ) . '/includes/ListTable.php';
        include_once dirname( __FILE__ ) . '/includes/EventsTable.php';
        include_once dirname( __FILE__ ) . '/includes/admin-settings.php';
        include_once dirname( __FILE__ ) . '/includes/manage-events.php';
        include_once dirname( __FILE__ ) . '/includes/gravity-forms.php';

    }

}

add_action( 'plugins_loaded', 'cio\init' );


function notify_gravity_forms_not_active() {

    if ( ! class_exists( '\GFAPI' ) ) { ?>

        <div class="notice notice-error is-dismissible">
            <p><?php _e( '<strong>Customer.io</strong> required Gravity Forms to be active.', 'cio' ); ?></p>
        </div>

    <?php }

}

add_action( 'admin_notices', 'cio\notify_gravity_forms_not_active' );


function activate() {

    include_once dirname( __FILE__ ) . '/includes/tables.php';

    create_tables();

}

register_activation_hook( __FILE__, 'cio\activate' );


function asset( $path = '' ) {

    return trailingslashit( plugin_dir_url( __FILE__ ) . 'assets/' ) . ltrim( $path, '/' );

}

