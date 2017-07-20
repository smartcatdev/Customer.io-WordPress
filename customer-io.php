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
	include_once dirname( __FILE__ ) . '/includes/ListTable.php';
	include_once dirname( __FILE__ ) . '/includes/FormsTable.php';
	include_once dirname( __FILE__ ) . '/includes/admin-settings.php';
	include_once dirname( __FILE__ ) . '/includes/tables.php';
	include_once dirname( __FILE__ ) . '/includes/form-mapping.php';

}

add_action( 'plugins_loaded', 'cio\init' );
