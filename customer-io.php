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
	include_once dirname( __FILE__ ) . '/includes/tables.php';
	include_once dirname( __FILE__ ) . '/includes/manage-events.php';

}

add_action( 'plugins_loaded', 'cio\init' );


function asset( $path = '' ) {

	return trailingslashit( plugin_dir_url( __FILE__ ) . 'assets/' ) . ltrim( $path, '/' );

}
