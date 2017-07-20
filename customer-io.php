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
	include_once dirname( __FILE__ ) . '/includes/admin_settings.php';

}

add_action( 'plugins_loaded', 'cio\init' );
