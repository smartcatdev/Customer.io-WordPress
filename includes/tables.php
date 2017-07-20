<?php

namespace cio;

function create_tables() {

	global $wpdb;

	include_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$q = "CREATE TABLE {$wpdb->prefix}cio_field_mappings (
			id 			INT PRIMARY KEY AUTO_INCREMENT,
			form_id 	INT NOT NULL,
			field_id    INT NOT NULL,
			data_name   TEXT,
			status      VARCHAR( 25 )
		 )";

	dbDelta( $q );

}

add_action( 'admin_init', 'cio\create_tables' );
