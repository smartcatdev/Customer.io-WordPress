<?php

namespace cio;


if( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

include_once 'constants.php';

global $wpdb;


$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}cio_events" );
