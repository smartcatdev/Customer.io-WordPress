<?php

namespace cio;

function create_tables() {

	global $wpdb;

	include_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$q = "CREATE TABLE {$wpdb->prefix}cio_events (
			id			INT AUTO_INCREMENT,
			form_id 	INT NOT NULL,
			event_name	VARCHAR( 25 ),
			status		VARCHAR( 25 ),
			id_field	INT NOT NULL,
			email_field	INT NOT NULL,
			field_map	TEXT,
			date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			UNIQUE KEY ID (id)
		 )";

	dbDelta( $q );

}

add_action( 'admin_init', 'cio\create_tables' );
