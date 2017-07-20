<?php

namespace cio;

function create_tables() {

	global $wpdb;

	include_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$q = "CREATE TABLE {$wpdb->prefix}cio_events (
			id 			INT PRIMARY KEY AUTO_INCREMENT,
			form_id 	INT NOT NULL,
			event       VARCHAR( 25 ),
			status      VARCHAR( 25 ),
			field_map   TEXT
		 )";

	dbDelta( $q );

}

add_action( 'admin_init', 'cio\create_tables' );
