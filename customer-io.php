<?php
/*
 * Plugin Name: Customer.io
 * Version: 1.0.0
 * Author: Smartcat
 */

namespace cio;


if ( !defined( 'ABSPATH' ) ) {
	die;
}


include_once 'constants.php';


function init() {

	include_once dirname( __FILE__ ) . '/includes/functions.php';
	include_once dirname( __FILE__ ) . '/includes/helpers.php';
	include_once dirname( __FILE__ ) . '/includes/ListTable.php';
	include_once dirname( __FILE__ ) . '/includes/EventsTable.php';
	include_once dirname( __FILE__ ) . '/includes/admin-settings.php';
	include_once dirname( __FILE__ ) . '/includes/manage-events.php';
	include_once dirname( __FILE__ ) . '/includes/gravity-forms.php';

}

add_action( 'plugins_loaded', 'cio\init' );


function activate() {

	include_once dirname( __FILE__ ) . '/includes/tables.php';

	create_tables();

}

register_activation_hook( __FILE__, 'cio\activate' );


function asset( $path = '' ) {

	return trailingslashit( plugin_dir_url( __FILE__ ) . 'assets/' ) . ltrim( $path, '/' );

}

